<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function showPublic(User $user): View
    {
        $blocks = $user->blocks()
            ->with('map')
            ->orderByDesc('created_at')
            ->get();

        $grades = $blocks->pluck('grade')
            ->filter(fn ($grade) => is_string($grade) && $grade !== '')
            ->map(fn (string $grade) => strtolower($grade))
            ->values();

        $bestGrade = $this->resolveBestGrade($grades->all());

        return view('users.public', [
            'userProfile' => $user,
            'blocks' => $blocks,
            'totalBlocks' => $blocks->count(),
            'bestGrade' => $bestGrade,
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
}
