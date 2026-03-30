<?php

namespace App\Http\Controllers;

use App\Models\KilterBlock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function showPublic(User $user): View
    {
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

        return view('users.public', [
            'userProfile' => $user,
            'completedBlocks' => $completedBlocks,
            'totalCompletedBlocks' => $completedBlocks->count(),
            'bestGrade' => $bestGrade,
            'difficultyPercent' => $difficultyPercent,
        ]);
    }

    public function settings(Request $request): View
    {
        return view('users.settings', [
            'userProfile' => $request->user(),
        ]);
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
