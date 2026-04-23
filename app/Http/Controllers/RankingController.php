<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function index(Request $request): View
    {
        $gradeWeights = array_flip($this->orderedGrades());
        $maxWeight = max(1, count($gradeWeights) - 1);
        $scoreTable = $this->scoreTable();
        $currentMonthStart = now()->startOfMonth();

        $users = User::query()
            ->select('id', 'username', 'name')
            ->orderByRaw('COALESCE(NULLIF(username, \'\'), name) asc')
            ->get();

        $stats = [];
        foreach ($users as $user) {
            $userId = (int) $user->id;
            $stats[$userId] = [
                'user_id' => $userId,
                'username' => (string) ($user->username ?: $user->name),
                'completions' => 0,
                'total_points' => 0.0,
                'difficulty_sum' => 0.0,
                'best_grade_weight' => -1,
                'best_grade' => '-',
                'monthly_completions' => 0,
                'monthly_creations' => 0,
            ];
        }

        $completionRows = DB::table('kilter_block_completions as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->join('kilter_blocks as b', 'b.id', '=', 'c.kilter_block_id')
            ->select('u.id as user_id', 'u.username', 'u.name', 'b.grade')
            ->orderBy('u.id')
            ->get();

        foreach ($completionRows as $row) {
            $grade = strtolower(trim((string) $row->grade));
            $weight = $gradeWeights[$grade] ?? 0;
            $blockPoints = (float) ($scoreTable[$grade] ?? 0.0);
            $normalizedDifficulty = $weight / $maxWeight;
            $userId = (int) $row->user_id;

            if (! isset($stats[$userId])) {
                continue;
            }

            $stats[$userId]['completions'] += 1;
            $stats[$userId]['total_points'] += $blockPoints;
            $stats[$userId]['difficulty_sum'] += $normalizedDifficulty;

            if ($weight > $stats[$userId]['best_grade_weight']) {
                $stats[$userId]['best_grade_weight'] = $weight;
                $stats[$userId]['best_grade'] = strtoupper($grade);
            }
        }

        $monthlyCompletions = DB::table('kilter_block_completions')
            ->select('user_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $currentMonthStart)
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        foreach ($monthlyCompletions as $userId => $total) {
            $userId = (int) $userId;
            if (! isset($stats[$userId])) {
                continue;
            }

            $stats[$userId]['monthly_completions'] = (int) $total;
        }

        $monthlyCreations = DB::table('kilter_blocks')
            ->select('user_id', DB::raw('count(*) as total'))
            ->whereNotNull('user_id')
            ->where('created_at', '>=', $currentMonthStart)
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        foreach ($monthlyCreations as $userId => $total) {
            $userId = (int) $userId;
            if (! isset($stats[$userId])) {
                continue;
            }

            $stats[$userId]['monthly_creations'] = (int) $total;
        }

        $rankingRows = array_values(array_map(static function (array $entry): array {
            $actualCompletions = (int) $entry['completions'];
            $avgDifficulty = $actualCompletions > 0
                ? $entry['difficulty_sum'] / $actualCompletions
                : 0.0;

            return [
                'user_id' => $entry['user_id'],
                'username' => $entry['username'],
                'completions' => $actualCompletions,
                'score' => round($entry['total_points'], 2),
                'avg_difficulty' => round($avgDifficulty * 100, 1),
                'best_grade' => $entry['best_grade'],
            ];
        }, $stats));

        $rankingRows = array_values(array_filter($rankingRows, static fn (array $entry): bool => $entry['score'] > 0));

        usort($rankingRows, static function (array $a, array $b): int {
            if ($a['score'] === $b['score']) {
                if ($a['completions'] === $b['completions']) {
                    if ($a['avg_difficulty'] === $b['avg_difficulty']) {
                        return strcmp($a['username'], $b['username']);
                    }

                    return $b['avg_difficulty'] <=> $a['avg_difficulty'];
                }

                return $b['completions'] <=> $a['completions'];
            }

            return $b['score'] <=> $a['score'];
        });

        $ranking = $this->paginateArray($rankingRows, 10, $request, 'page');

        $highlights = [
            [
                'title' => 'Bloke gehien eginda',
                'user' => $this->topUser($stats, static fn (array $entry): array => [
                    'primary' => (int) $entry['completions'],
                    'secondary' => (float) $entry['total_points'],
                ]),
                'format' => static fn (?array $user): string => $user ? ((int) $user['completions']).' bloke' : '-',
            ],
            [
                'title' => 'Gradu altuena',
                'user' => $this->topUser($stats, static fn (array $entry): array => [
                    'primary' => (int) $entry['best_grade_weight'],
                    'secondary' => (int) $entry['completions'],
                ]),
                'format' => static fn (?array $user): string => $user ? (string) $user['best_grade'] : '-',
            ],
            [
                'title' => 'Zailtasun handiena',
                'user' => $this->topUser($stats, static fn (array $entry): array => [
                    'primary' => (int) $entry['completions'] > 0
                        ? (($entry['difficulty_sum'] / $entry['completions']) * 100)
                        : 0.0,
                    'secondary' => (int) $entry['completions'],
                ]),
                'format' => static fn (?array $user): string => $user && (int) $user['completions'] > 0
                    ? number_format(((float) $user['difficulty_sum'] / (int) $user['completions']) * 100, 1).'%'
                    : '-',
            ],
            [
                'title' => 'Hilabeteko aktiboena',
                'user' => $this->topUser($stats, static fn (array $entry): array => [
                    'primary' => (int) $entry['monthly_completions'] + (int) $entry['monthly_creations'],
                    'secondary' => (int) $entry['monthly_completions'],
                ]),
                'format' => static fn (?array $user): string => $user
                    ? (((int) $user['monthly_completions'] + (int) $user['monthly_creations']).' ekintza')
                    : '-',
            ],
        ];

        return view('ranking.index', [
            'ranking' => $ranking,
            'highlights' => $highlights,
            'scoreTable' => $scoreTable,
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

    private function paginateArray(array $items, int $perPage, Request $request, string $pageName): LengthAwarePaginator
    {
        $page = max(1, (int) $request->query($pageName, 1));
        $total = count($items);
        $slice = array_slice($items, ($page - 1) * $perPage, $perPage);

        return (new LengthAwarePaginator($slice, $total, $perPage, $page, [
            'path' => $request->url(),
            'pageName' => $pageName,
        ]))->withQueryString();
    }

    private function topUser(array $stats, callable $metric): ?array
    {
        $best = null;
        $bestPrimary = null;
        $bestSecondary = null;

        foreach ($stats as $entry) {
            ['primary' => $primary, 'secondary' => $secondary] = $metric($entry);
            if ($primary <= 0) {
                continue;
            }

            if ($best === null
                || $primary > $bestPrimary
                || ($primary === $bestPrimary && $secondary > $bestSecondary)
                || ($primary === $bestPrimary && $secondary === $bestSecondary && strcmp($entry['username'], $best['username']) < 0)
            ) {
                $best = $entry;
                $bestPrimary = $primary;
                $bestSecondary = $secondary;
            }
        }

        return $best;
    }

    /**
     * @return array<string, float>
     */
    private function scoreTable(): array
    {
        return [
            '5a' => 5,
            '5a+' => 6,
            '5b' => 10,
            '5b+' => 12,
            '5c' => 17,
            '5c+' => 22,
            '6a' => 30,
            '6a+' => 38,
            '6b' => 47,
            '6b+' => 60,
            '6c' => 76,
            '6c+' => 96,
            '7a' => 122,
            '7a+' => 154,
            '7b' => 194,
            '7b+' => 244,
            '7c' => 308,
            '7c+' => 388,
            '8a' => 490,
            '8a+' => 618,
            '8b' => 778,
            '8b+' => 980,
            '8c' => 1234,
            '8c+' => 1554,
            '9a' => 1958,
            '9a+' => 2466,
            '9b' => 3106,
            '9b+' => 3912,
            '9c' => 4928,
        ];
    }
}
