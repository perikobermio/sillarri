<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function index(): View
    {
        $gradeWeights = array_flip($this->orderedGrades());
        $maxWeight = max(1, count($gradeWeights) - 1);

        $rows = DB::table('kilter_block_completions as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->join('kilter_blocks as b', 'b.id', '=', 'c.kilter_block_id')
            ->select('u.id as user_id', 'u.username', 'u.name', 'b.grade')
            ->orderBy('u.id')
            ->get();

        $stats = [];
        foreach ($rows as $row) {
            $grade = strtolower(trim((string) $row->grade));
            $weight = $gradeWeights[$grade] ?? 0;
            $normalizedDifficulty = $weight / $maxWeight;

            // Fair balance: count matters (base points), but hard blocks earn meaningfully more.
            $blockPoints = 80 + (70 * $normalizedDifficulty) + (20 * $normalizedDifficulty * $normalizedDifficulty);

            $key = (int) $row->user_id;
            if (! isset($stats[$key])) {
                $stats[$key] = [
                    'user_id' => $key,
                    'username' => (string) ($row->username ?: $row->name),
                    'completions' => 0,
                    'total_points' => 0.0,
                    'difficulty_sum' => 0.0,
                ];
            }

            $stats[$key]['completions'] += 1;
            $stats[$key]['total_points'] += $blockPoints;
            $stats[$key]['difficulty_sum'] += $normalizedDifficulty;
        }

        $ranking = array_values(array_map(static function (array $entry): array {
            $completions = max(1, (int) $entry['completions']);
            $avgDifficulty = $entry['difficulty_sum'] / $completions;

            return [
                'user_id' => $entry['user_id'],
                'username' => $entry['username'],
                'completions' => $entry['completions'],
                'score' => round($entry['total_points'], 2),
                'avg_difficulty' => round($avgDifficulty * 100, 1),
            ];
        }, $stats));

        usort($ranking, static function (array $a, array $b): int {
            if ($a['score'] === $b['score']) {
                if ($a['completions'] === $b['completions']) {
                    return $b['avg_difficulty'] <=> $a['avg_difficulty'];
                }
                return $b['completions'] <=> $a['completions'];
            }
            return $b['score'] <=> $a['score'];
        });

        return view('ranking.index', [
            'ranking' => $ranking,
        ]);
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
