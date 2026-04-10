<?php

namespace App\Http\Controllers;

use App\Models\KilterBlock;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function showPublic(User $user): View
    {
        $viewer = auth()->user();
        $isOwnProfile = $viewer && (int) $viewer->id === (int) $user->id;

        $completedIds = DB::table('kilter_block_completions')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->pluck('kilter_block_id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        $completedBlocks = count($completedIds) > 0
            ? KilterBlock::query()
                ->with('map')
                ->whereIn('id', $completedIds)
                ->get()
                ->sortBy(fn (KilterBlock $block) => array_search((int) $block->id, $completedIds, true))
                ->values()
            : collect();

        $completedGrades = $completedBlocks->pluck('grade')
            ->filter(fn ($grade) => is_string($grade) && $grade !== '')
            ->map(fn (string $grade) => strtolower($grade))
            ->values();

        $bestGrade = $this->resolveBestGrade($completedGrades->all());
        $difficultyPercent = $this->averageDifficultyPercent($completedGrades->all());

        $createdBlocks = KilterBlock::query()
            ->with('map')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $pendingRecotes = collect();
        if ($isOwnProfile) {
            $rows = DB::table('kilter_block_recotations as r')
                ->join('kilter_blocks as b', 'b.id', '=', 'r.kilter_block_id')
                ->where('b.user_id', $user->id)
                ->select('r.kilter_block_id', 'r.grade', DB::raw('count(*) as total'))
                ->groupBy('r.kilter_block_id', 'r.grade')
                ->get();

            $countsByBlock = [];
            foreach ($rows as $row) {
                $blockId = (int) $row->kilter_block_id;
                $grade = strtolower(trim((string) $row->grade));
                $countsByBlock[$blockId][$grade] = (int) $row->total;
            }

            $blockIds = array_keys($countsByBlock);
            if (!empty($blockIds)) {
                $blocksById = KilterBlock::query()
                    ->whereIn('id', $blockIds)
                    ->get()
                    ->keyBy('id');

                $order = $this->orderedGrades();
                $weights = array_flip($order);

                $pendingRecotes = collect($countsByBlock)->map(function (array $counts, int $blockId) use ($blocksById, $weights) {
                    $block = $blocksById->get($blockId);
                    if (!$block) {
                        return null;
                    }

                    $top = null;
                    $topCount = -1;
                    $topWeight = PHP_INT_MAX;
                    foreach ($counts as $grade => $count) {
                        $weight = $weights[$grade] ?? PHP_INT_MAX;
                        if ($count > $topCount || ($count === $topCount && $weight < $topWeight)) {
                            $topCount = $count;
                            $topWeight = $weight;
                            $top = $grade;
                        }
                    }

                    return [
                        'block' => $block,
                        'suggested_grade' => $top ? strtoupper($top) : null,
                        'current_grade' => strtoupper((string) $block->grade),
                    ];
                })->filter()->values();
            }
        }

        return view('users.public', [
            'userProfile' => $user,
            'completedBlocks' => $completedBlocks,
            'totalCompletedBlocks' => $completedBlocks->count(),
            'bestGrade' => $bestGrade,
            'difficultyPercent' => $difficultyPercent,
            'createdBlocks' => $createdBlocks,
            'pendingRecotes' => $pendingRecotes,
            'isOwnProfile' => $isOwnProfile,
        ]);
    }

    public function settings(Request $request): View
    {
        return view('users.settings', [
            'userProfile' => $request->user(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:20480'],
        ];

        $data = $request->validate($rules);

        $user->name = trim((string) $data['name']);
        $user->email = strtolower(trim((string) $data['email']));
        $user->phone = filled($data['phone'] ?? null)
            ? trim((string) $data['phone'])
            : null;

        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make((string) $data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()
            ->route('settings')
            ->with('status', 'Ezarpenak ondo gorde dira.');
    }

    /**
     * @param list<string> $grades
     */
    private function resolveBestGrade(array $grades): string
    {
        $order = $this->orderedGrades();
        $weight = array_flip($order);

        $best = null;
        $bestWeight = -1;

        foreach ($grades as $grade) {
            if (! isset($weight[$grade])) {
                continue;
            }

            if ($weight[$grade] > $bestWeight) {
                $bestWeight = $weight[$grade];
                $best = $grade;
            }
        }

        return $best !== null ? strtoupper($best) : '-';
    }

    /**
     * @return list<string>
     */
    private function orderedGrades(): array
    {
        $result = [];
        $suffixes = ['a', 'a+', 'b', 'b+', 'c', 'c+'];

        for ($level = 5; $level <= 9; $level++) {
            foreach ($suffixes as $suffix) {
                $grade = $level.$suffix;
                $result[] = $grade;

                if ($grade === '9c') {
                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * @param list<string> $grades
     */
    private function averageDifficultyPercent(array $grades): float
    {
        $order = $this->orderedGrades();
        $weight = array_flip($order);
        $maxWeight = max(1, count($order) - 1);

        $sum = 0.0;
        $count = 0;
        foreach ($grades as $grade) {
            if (! isset($weight[$grade])) {
                continue;
            }
            $sum += ($weight[$grade] / $maxWeight) * 100;
            $count++;
        }

        if ($count === 0) {
            return 0.0;
        }

        return round($sum / $count, 1);
    }
}
