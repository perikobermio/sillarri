<?php

namespace App\Http\Controllers;

use App\Models\KilterBlock;
use App\Models\KilterMap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KilterController extends Controller
{
    public function show(Request $request, KilterBlock $block): View
    {
        $block->loadMissing(['map', 'creator']);
        $user = $request->user();
        $isCompleted = false;
        $userVote = null;
        $userRecote = null;

        if ($user) {
            $isCompleted = DB::table('kilter_block_completions')
                ->where('user_id', $user->id)
                ->where('kilter_block_id', $block->id)
                ->exists();

            $userVoteValue = DB::table('kilter_block_votes')
                ->where('user_id', $user->id)
                ->where('kilter_block_id', $block->id)
                ->value('value');
            $userVote = is_numeric($userVoteValue) ? (float) $userVoteValue : null;

            $userRecoteValue = DB::table('kilter_block_recotations')
                ->where('user_id', $user->id)
                ->where('kilter_block_id', $block->id)
                ->value('grade');
            $userRecote = is_string($userRecoteValue) ? $userRecoteValue : null;
        }

        $rating = $this->ratingForBlocks([(int) $block->id])[(int) $block->id] ?? ['avg' => 5.0, 'count' => 0];
        $grades = $this->grades();
        $recotationSummary = $this->recotationSummary((int) $block->id, $grades);
        $recotationEntries = DB::table('kilter_block_recotations as r')
            ->join('users as u', 'u.id', '=', 'r.user_id')
            ->where('r.kilter_block_id', $block->id)
            ->select('r.grade', 'u.username')
            ->orderBy('r.created_at')
            ->get()
            ->map(static function ($row): array {
                return [
                    'grade' => (string) $row->grade,
                    'username' => (string) $row->username,
                ];
            })
            ->all();

        return view('kilter.show', [
            'block' => $block,
            'isCompleted' => $isCompleted,
            'userVote' => $userVote,
            'userRecote' => $userRecote,
            'ratingAverage' => (float) $rating['avg'],
            'ratingCount' => (int) $rating['count'],
            'grades' => $grades,
            'recotationSummary' => $recotationSummary,
            'recotationEntries' => $recotationEntries,
        ]);
    }

    public function index(Request $request): View
    {
        $clearRequested = $request->query('clear') === '1';
        if ($clearRequested) {
            $request->session()->forget('kilterFilters');
        }

        $incoming = [
            'q' => trim((string) $request->query('q', '')),
            'creator' => trim((string) $request->query('creator', '')),
            'completed' => trim((string) $request->query('completed', 'all')),
            'location' => trim((string) $request->query('location', '')),
            'grades' => $request->query('grade', []),
            'order_field' => trim((string) $request->query('order_field', '')),
            'order_dir' => trim((string) $request->query('order_dir', '')),
        ];

        $hasIncomingFilters = $incoming['q'] !== ''
            || $incoming['creator'] !== ''
            || $incoming['location'] !== ''
            || ($incoming['completed'] !== '' && $incoming['completed'] !== 'all')
            || (is_array($incoming['grades']) ? count($incoming['grades']) > 0 : (string) $incoming['grades'] !== '')
            || $incoming['order_field'] !== ''
            || $incoming['order_dir'] !== '';

        if ($hasIncomingFilters) {
            $request->session()->put('kilterFilters', $incoming);
        }

        $stored = $hasIncomingFilters || $clearRequested ? null : $request->session()->get('kilterFilters');
        $stored = is_array($stored) ? $stored : [];

        $search = trim((string) ($hasIncomingFilters ? $incoming['q'] : ($stored['q'] ?? '')));
        $creatorQuery = trim((string) ($hasIncomingFilters ? $incoming['creator'] : ($stored['creator'] ?? '')));
        $selectedCreator = ctype_digit($creatorQuery) ? (int) $creatorQuery : null;
        $locationQuery = trim((string) ($hasIncomingFilters ? $incoming['location'] : ($stored['location'] ?? '')));
        $completedFilterQuery = trim((string) ($hasIncomingFilters ? $incoming['completed'] : ($stored['completed'] ?? 'all')));
        $selectedCompletedFilter = in_array($completedFilterQuery, ['all', 'done', 'pending'], true)
            ? $completedFilterQuery
            : 'all';
        $grades = $this->grades();
        $gradeQuery = $hasIncomingFilters ? $incoming['grades'] : ($stored['grades'] ?? []);
        $gradeList = is_array($gradeQuery) ? $gradeQuery : [$gradeQuery];
        $selectedGrades = array_values(array_unique(array_filter(array_map(function ($value): string {
            return strtolower(trim((string) $value));
        }, $gradeList))));
        $selectedGrades = array_values(array_intersect($selectedGrades, $grades));
        $allowedOrderFields = ['rating', 'completed', 'grade', 'created_at'];
        $orderField = $hasIncomingFilters ? $incoming['order_field'] : ($stored['order_field'] ?? '');
        $orderField = in_array($orderField, $allowedOrderFields, true) ? $orderField : '';
        $orderDir = $hasIncomingFilters ? $incoming['order_dir'] : ($stored['order_dir'] ?? 'desc');
        $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';
        $filtersActive = $search !== ''
            || $creatorQuery !== ''
            || $locationQuery !== ''
            || $selectedCompletedFilter !== 'all'
            || count($selectedGrades) > 0
            || $orderField !== '';

        $user = $request->user();

        $locations = KilterBlock::query()
            ->whereNotNull('kokapena')
            ->where('kokapena', '!=', '')
            ->select('kokapena')
            ->distinct()
            ->orderBy('kokapena')
            ->pluck('kokapena')
            ->all();

        $blocks = KilterBlock::query()
            ->select('kilter_blocks.*')
            ->addSelect([
                'completed_count' => DB::table('kilter_block_completions')
                    ->selectRaw('count(*)')
                    ->whereColumn('kilter_block_completions.kilter_block_id', 'kilter_blocks.id'),
                'rating_avg' => DB::table('kilter_block_votes')
                    ->selectRaw('coalesce(avg(value), 0)'),
            ])
            ->with(['map', 'creator'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($locationQuery !== '', function ($query) use ($locationQuery): void {
                $query->where('kokapena', $locationQuery);
            })
            ->when(count($selectedGrades) > 0, function ($query) use ($selectedGrades): void {
                $query->whereIn(DB::raw('LOWER(grade)'), $selectedGrades);
            })
            ->when($selectedCreator !== null, function ($query) use ($selectedCreator): void {
                $query->where('user_id', $selectedCreator);
            })
            ->when($selectedCompletedFilter !== 'all' && $user, function ($query) use ($selectedCompletedFilter, $user): void {
                if ($selectedCompletedFilter === 'done') {
                    $query->whereExists(function ($sub) use ($user): void {
                        $sub->select(DB::raw(1))
                            ->from('kilter_block_completions')
                            ->whereColumn('kilter_block_completions.kilter_block_id', 'kilter_blocks.id')
                            ->where('kilter_block_completions.user_id', $user->id);
                    });
                    return;
                }

                $query->whereNotExists(function ($sub) use ($user): void {
                    $sub->select(DB::raw(1))
                        ->from('kilter_block_completions')
                        ->whereColumn('kilter_block_completions.kilter_block_id', 'kilter_blocks.id')
                        ->where('kilter_block_completions.user_id', $user->id);
                });
            });

        if ($orderField === 'rating') {
            $blocks->orderBy('rating_avg', $orderDir);
        } elseif ($orderField === 'completed') {
            $blocks->orderBy('completed_count', $orderDir);
        } elseif ($orderField === 'grade') {
            $cases = [];
            foreach ($grades as $index => $grade) {
                $gradeValue = strtolower($grade);
                $cases[] = "WHEN LOWER(grade) = '{$gradeValue}' THEN {$index}";
            }
            $orderExpr = 'CASE '.implode(' ', $cases).' ELSE 999 END';
            $blocks->orderByRaw($orderExpr.' '.$orderDir);
        } elseif ($orderField === 'created_at') {
            $blocks->orderBy('created_at', $orderDir);
        } else {
            $blocks->orderByDesc('created_at');
        }

        $blocks = $blocks->get();

        $creators = User::query()
            ->select('users.id', 'users.name')
            ->join('kilter_blocks', 'kilter_blocks.user_id', '=', 'users.id')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        $completedBlockIds = [];
        if ($user) {
            $completedBlockIds = DB::table('kilter_block_completions')
                ->where('user_id', $user->id)
                ->pluck('kilter_block_id')
                ->map(static fn ($id): int => (int) $id)
                ->all();
        }

        $blockIds = $blocks->pluck('id')->map(static fn ($id): int => (int) $id)->all();
        $ratingsByBlock = $this->ratingForBlocks($blockIds);
        $recotationCountsByBlock = [];
        if (count($blockIds) > 0) {
            $recotationCountsByBlock = DB::table('kilter_block_recotations')
                ->whereIn('kilter_block_id', $blockIds)
                ->select('kilter_block_id', DB::raw('count(*) as total'))
                ->groupBy('kilter_block_id')
                ->pluck('total', 'kilter_block_id')
                ->map(static fn ($value): int => (int) $value)
                ->all();
        }

        return view('kilter.index', [
            'blocks' => $blocks,
            'search' => $search,
            'grades' => $grades,
            'selectedGrades' => $selectedGrades,
            'creators' => $creators,
            'selectedCreator' => $selectedCreator,
            'locations' => $locations,
            'selectedLocation' => $locationQuery,
            'completedBlockIds' => $completedBlockIds,
            'selectedCompletedFilter' => $selectedCompletedFilter,
            'selectedOrderField' => $orderField,
            'selectedOrderDir' => $orderDir,
            'ratingsByBlock' => $ratingsByBlock,
            'recotationCountsByBlock' => $recotationCountsByBlock,
            'filtersActive' => $filtersActive,
        ]);
    }

    public function create(Request $request): View
    {
        $maps = KilterMap::query()->orderBy('name')->get();
        $locations = KilterBlock::query()
            ->whereNotNull('kokapena')
            ->where('kokapena', '!=', '')
            ->select('kokapena')
            ->distinct()
            ->orderBy('kokapena')
            ->pluck('kokapena')
            ->all();

        return view('kilter.create', [
            'maps' => $maps,
            'grades' => $this->grades(),
            'locations' => $locations,
            'isMobileClient' => $this->isMobileRequest($request),
        ]);
    }

    public function edit(Request $request, KilterBlock $block): View
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $isOwner = (int) $block->user_id === (int) $user->id;
        $isAdmin = (bool) $user->is_admin;
        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Ez daukazu bloke hau editatzeko baimenik.');
        }

        $maps = KilterMap::query()->orderBy('name')->get();
        $locations = KilterBlock::query()
            ->whereNotNull('kokapena')
            ->where('kokapena', '!=', '')
            ->select('kokapena')
            ->distinct()
            ->orderBy('kokapena')
            ->pluck('kokapena')
            ->all();

        return view('kilter.edit', [
            'block' => $block,
            'maps' => $maps,
            'grades' => $this->grades(),
            'locations' => $locations,
            'isMobileClient' => $this->isMobileRequest($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $payload = $this->validatedBlockPayload($request);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['boulder' => $e->getMessage()])->withInput();
        }

        $block = KilterBlock::create([
            'name' => $payload['name'],
            'description' => $payload['description'],
            'grade' => $payload['grade'],
            'kokapena' => $payload['kokapena'],
            'map_id' => $payload['map_id'],
            'user_id' => $request->user()->id,
            'boulder' => $payload['boulder'],
        ]);

        DB::table('kilter_block_votes')->updateOrInsert(
            [
                'user_id' => $request->user()->id,
                'kilter_block_id' => $block->id,
            ],
            [
                'value' => 5.0,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return redirect()
            ->route('kilter')
            ->with('status', 'Blokea ondo sortu da.');
    }

    public function update(Request $request, KilterBlock $block): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $isOwner = (int) $block->user_id === (int) $user->id;
        $isAdmin = (bool) $user->is_admin;
        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Ez daukazu bloke hau editatzeko baimenik.');
        }

        try {
            $payload = $this->validatedBlockPayload($request);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['boulder' => $e->getMessage()])->withInput();
        }

        $originalBoulder = (string) $block->boulder;
        $newBoulder = (string) $payload['boulder'];

        $block->update([
            'name' => $payload['name'],
            'description' => $payload['description'],
            'grade' => $payload['grade'],
            'kokapena' => $payload['kokapena'],
            'map_id' => $payload['map_id'],
            'boulder' => $newBoulder,
        ]);

        $coordinatesChanged = trim($originalBoulder) !== trim($newBoulder);

        if ($coordinatesChanged) {
            DB::table('kilter_block_completions')
                ->where('kilter_block_id', $block->id)
                ->delete();
            DB::table('kilter_block_votes')
                ->where('kilter_block_id', $block->id)
                ->delete();
            DB::table('kilter_block_recotations')
                ->where('kilter_block_id', $block->id)
                ->delete();

            DB::table('kilter_block_votes')->insert([
                'user_id' => $block->user_id,
                'kilter_block_id' => $block->id,
                'value' => 5.0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $message = $coordinatesChanged
            ? 'Blokea editatu da. Realizazio eta bozka guztiak berrabiarazi dira.'
            : 'Blokea editatu da.';

        return redirect()
            ->route('kilter.show', $block)
            ->with('status', $message);
    }

    public function storeMap(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:20480'],
        ]);

        [$path, $physicalPath] = $this->storeNormalizedMapImage($request->file('image'));

        $map = KilterMap::create([
            'name' => $data['name'],
            'image' => $path,
            'image_physical_path' => $physicalPath,
        ]);

        return response()->json([
            'id' => $map->id,
            'name' => $map->name,
            'image' => $map->image,
            'image_physical_path' => $map->image_physical_path,
            'image_url' => '/storage/'.$map->image,
        ]);
    }

    public function destroy(Request $request, KilterBlock $block): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $isOwner = (int) $block->user_id === (int) $user->id;
        $isAdmin = (bool) $user->is_admin;
        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Ez daukazu bloke hau ezabatzeko baimenik.');
        }

        $block->delete();

        return redirect()
            ->route('kilter')
            ->with('status', 'Blokea ondo ezabatu da.');
    }

    public function toggleCompleted(Request $request, KilterBlock $block): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $completion = DB::table('kilter_block_completions')
            ->where('user_id', $user->id)
            ->where('kilter_block_id', $block->id);

        if ($completion->exists()) {
            $completion->delete();
            return back()->with('status', 'Blokea eginda zerrendatik kendu da.');
        }

        DB::table('kilter_block_completions')->insert([
            'user_id' => $user->id,
            'kilter_block_id' => $block->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Blokea eginda bezala markatu da.');
    }

    public function vote(Request $request, KilterBlock $block): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $data = $request->validate([
            'value' => ['required', 'numeric', 'min:1', 'max:10'],
        ]);

        $value = round((float) $data['value'] * 2) / 2;
        $value = max(1.0, min(10.0, $value));

        DB::table('kilter_block_votes')->updateOrInsert(
            [
                'user_id' => $user->id,
                'kilter_block_id' => $block->id,
            ],
            [
                'value' => $value,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('status', 'Bozka ondo gorde da.');
    }

    public function recote(Request $request, KilterBlock $block): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if ((int) $block->user_id === (int) $user->id) {
            abort(403, 'Ezin duzu zure blokea berriz graduatu.');
        }

        $data = $request->validate([
            'grade' => ['required', Rule::in($this->grades())],
        ]);

        DB::table('kilter_block_recotations')->updateOrInsert(
            [
                'user_id' => $user->id,
                'kilter_block_id' => $block->id,
            ],
            [
                'grade' => (string) $data['grade'],
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('status', 'Recotazioa ondo gorde da.');
    }

    public function resolveRecote(Request $request, KilterBlock $block): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $isOwner = (int) $block->user_id === (int) $user->id;
        $isAdmin = (bool) $user->is_admin;
        if (! $isOwner && ! $isAdmin) {
            abort(403);
        }

        $data = $request->validate([
            'decision' => ['required', Rule::in(['accept', 'reject'])],
        ]);

        $grades = $this->grades();
        $summary = $this->recotationSummary((int) $block->id, $grades);
        $topGrade = $summary['top'] ?? null;

        if (! $topGrade) {
            return back()->with('status', 'Ez dago recotaziorik berrikusteko.');
        }

        if ($data['decision'] === 'accept') {
            $block->update(['grade' => $topGrade]);
        }

        DB::table('kilter_block_recotations')
            ->where('kilter_block_id', $block->id)
            ->delete();

        return back()->with('status', 'Recotazioak eguneratu dira.');
    }

    /**
     * Store map image normalized to a max height to avoid huge uploads.
     *
     * @return array{0:string,1:string}
     */
    private function storeNormalizedMapImage(\Illuminate\Http\UploadedFile $file): array
    {
        $disk = Storage::disk('public');

        // Fallback to raw storage if GD is unavailable.
        if (! function_exists('imagecreatefromstring')) {
            $path = $file->store('kilter_maps', 'public');
            return [$path, $disk->path($path)];
        }

        $raw = @file_get_contents($file->getRealPath());
        if ($raw === false) {
            $path = $file->store('kilter_maps', 'public');
            return [$path, $disk->path($path)];
        }

        $source = @imagecreatefromstring($raw);
        if ($source === false) {
            $path = $file->store('kilter_maps', 'public');
            return [$path, $disk->path($path)];
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            imagedestroy($source);
            $path = $file->store('kilter_maps', 'public');
            return [$path, $disk->path($path)];
        }

        $targetMaxHeight = 1600;
        $ratio = min(1, $targetMaxHeight / $sourceHeight);
        $targetWidth = max(1, (int) round($sourceWidth * $ratio));
        $targetHeight = max(1, (int) round($sourceHeight * $ratio));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        $path = 'kilter_maps/'.Str::uuid().'.webp';
        $supportsWebp = function_exists('imagewebp');
        $encoded = false;
        $binary = '';

        if ($supportsWebp) {
            ob_start();
            $encoded = imagewebp($canvas, null, 85);
            $binary = (string) ob_get_clean();
        }

        imagedestroy($canvas);
        imagedestroy($source);

        if (! $supportsWebp || ! $encoded || $binary === '') {
            $path = $file->store('kilter_maps', 'public');
            return [$path, $disk->path($path)];
        }

        $disk->put($path, $binary);

        return [$path, $disk->path($path)];
    }

    private function isMobileRequest(Request $request): bool
    {
        $ua = strtolower((string) $request->userAgent());

        return str_contains($ua, 'android')
            || str_contains($ua, 'iphone')
            || str_contains($ua, 'ipad')
            || str_contains($ua, 'ipod')
            || str_contains($ua, 'mobile');
    }

    /**
     * @return array{name:string,description:string,grade:string,map_id:int,boulder:string}
     */
    private function validatedBlockPayload(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'grade' => ['required', Rule::in($this->grades())],
            'kokapena' => ['required', 'string', 'max:120'],
            'map_id' => ['required', 'integer', 'exists:kilter_maps,id'],
            'boulder' => ['required', 'json'],
        ]);

        $normalizedBoulder = $this->normalizeBoulderJson((string) $data['boulder']);

        return [
            'name' => (string) $data['name'],
            'description' => (string) $data['description'],
            'grade' => (string) $data['grade'],
            'kokapena' => (string) $data['kokapena'],
            'map_id' => (int) $data['map_id'],
            'boulder' => $normalizedBoulder,
        ];
    }

    private function normalizeBoulderJson(string $json): string
    {
        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            throw new \InvalidArgumentException('Gutxienez koordenatu bat markatu behar duzu mapan.');
        }

        $mode = 'points';
        $coords = $decoded;
        if (array_key_exists('points', $decoded)) {
            $modeValue = $decoded['mode'] ?? 'points';
            if (! in_array($modeValue, ['points', 'line'], true)) {
                throw new \InvalidArgumentException('Koordenatuen formatua ez da baliozkoa.');
            }
            $mode = $modeValue;
            $coords = $decoded['points'];
        }

        if (! is_array($coords) || count($coords) === 0) {
            throw new \InvalidArgumentException('Gutxienez koordenatu bat markatu behar duzu mapan.');
        }

        $validTypes = ['pie', 'mano_pie', 'comienzo', 'top'];
        $validSizes = ['pequeno', 'mediano', 'grande', 'gigante'];
        $normalizedPoints = [];

        foreach ($coords as $point) {
            if (! is_array($point)) {
                throw new \InvalidArgumentException('Koordenatuen formatua ez da baliozkoa.');
            }

            $x = $point['x'] ?? null;
            $y = $point['y'] ?? null;
            $type = $point['type'] ?? null;
            $size = $point['size'] ?? null;

            if (! is_numeric($x) || ! is_numeric($y)) {
                throw new \InvalidArgumentException('Puntu bakoitzak koordenatu numerikoak izan behar ditu.');
            }

            if ((float) $x < 0 || (float) $x > 100 || (float) $y < 0 || (float) $y > 100) {
                throw new \InvalidArgumentException('Koordenatuek 0 eta 100 artean egon behar dute.');
            }

            if ($mode === 'points') {
                if (! in_array($type, $validTypes, true) || ! in_array($size, $validSizes, true)) {
                    throw new \InvalidArgumentException('Puntu bakoitzak mota eta tamaina baliozkoak izan behar ditu.');
                }

                $normalizedPoints[] = [
                    'x' => round((float) $x, 3),
                    'y' => round((float) $y, 3),
                    'type' => $type,
                    'size' => $size,
                ];
            } else {
                $normalizedPoints[] = [
                    'x' => round((float) $x, 3),
                    'y' => round((float) $y, 3),
                ];
            }
        }

        $normalizedBoulder = json_encode([
            'mode' => $mode,
            'points' => $normalizedPoints,
        ]);
        if (! is_string($normalizedBoulder)) {
            throw new \InvalidArgumentException('Koordenatuak ezin izan dira gorde.');
        }

        return $normalizedBoulder;
    }

    /**
     * @param list<string> $grades
     * @return array{counts:array<string,int>,top:?string,total:int}
     */
    private function recotationSummary(int $blockId, array $grades): array
    {
        $rows = DB::table('kilter_block_recotations')
            ->select('grade', DB::raw('count(*) as total'))
            ->where('kilter_block_id', $blockId)
            ->groupBy('grade')
            ->get();

        $counts = [];
        $total = 0;
        foreach ($rows as $row) {
            $grade = (string) $row->grade;
            $count = (int) $row->total;
            $counts[$grade] = $count;
            $total += $count;
        }

        if ($total === 0) {
            return ['counts' => [], 'top' => null, 'total' => 0];
        }

        $order = array_flip($grades);
        uksort($counts, function (string $a, string $b) use ($counts, $order): int {
            $countDiff = $counts[$b] <=> $counts[$a];
            if ($countDiff !== 0) {
                return $countDiff;
            }
            return ($order[$a] ?? 0) <=> ($order[$b] ?? 0);
        });

        $top = array_key_first($counts);

        return ['counts' => $counts, 'top' => $top, 'total' => $total];
    }

    /**
     * @param list<int> $blockIds
     * @return array<int, array{avg: float, count: int}>
     */
    private function ratingForBlocks(array $blockIds): array
    {
        if (count($blockIds) === 0) {
            return [];
        }

        $rows = DB::table('kilter_block_votes')
            ->select(
                'kilter_block_id',
                DB::raw('AVG(value) as avg_value'),
                DB::raw('COUNT(*) as votes_count')
            )
            ->whereIn('kilter_block_id', $blockIds)
            ->groupBy('kilter_block_id')
            ->get();

        $result = [];
        foreach ($blockIds as $blockId) {
            $result[(int) $blockId] = ['avg' => 5.0, 'count' => 0];
        }

        foreach ($rows as $row) {
            $id = (int) $row->kilter_block_id;
            $avg = is_numeric($row->avg_value) ? (float) $row->avg_value : 5.0;
            $count = is_numeric($row->votes_count) ? (int) $row->votes_count : 0;
            $result[$id] = ['avg' => round($avg, 2), 'count' => $count];
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    private function grades(): array
    {
        $grades = [];
        $suffixes = ['a', 'a+', 'b', 'b+', 'c', 'c+'];

        for ($level = 5; $level <= 9; $level++) {
            foreach ($suffixes as $suffix) {
                $grade = $level.$suffix;
                $grades[] = $grade;

                if ($grade === '9c') {
                    return $grades;
                }
            }
        }

        return $grades;
    }
}
