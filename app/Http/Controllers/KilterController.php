<?php

namespace App\Http\Controllers;

use App\Models\KilterBlock;
use App\Models\KilterMap;
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
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $grades = $this->grades();
        $gradeQuery = $request->query('grade', []);
        $gradeList = is_array($gradeQuery) ? $gradeQuery : [$gradeQuery];
        $selectedGrades = array_values(array_unique(array_filter(array_map(function ($value): string {
            return strtolower(trim((string) $value));
        }, $gradeList))));
        $selectedGrades = array_values(array_intersect($selectedGrades, $grades));

        $blocks = KilterBlock::query()
            ->with(['map', 'creator'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(count($selectedGrades) > 0, function ($query) use ($selectedGrades): void {
                $query->whereIn(DB::raw('LOWER(grade)'), $selectedGrades);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('kilter.index', [
            'blocks' => $blocks,
            'search' => $search,
            'grades' => $grades,
            'selectedGrades' => $selectedGrades,
        ]);
    }

    public function create(Request $request): View
    {
        $maps = KilterMap::query()->orderBy('name')->get();

        return view('kilter.create', [
            'maps' => $maps,
            'grades' => $this->grades(),
            'isMobileClient' => $this->isMobileRequest($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'grade' => ['required', Rule::in($this->grades())],
            'map_id' => ['required', 'integer', 'exists:kilter_maps,id'],
            'boulder' => ['required', 'json'],
        ]);

        $coords = json_decode($data['boulder'], true);
        if (! is_array($coords) || count($coords) === 0) {
            return back()->withErrors(['boulder' => 'Debes marcar al menos una coordenada en el mapa.'])->withInput();
        }

        $validTypes = ['pie', 'mano_pie', 'comienzo', 'top'];
        $validSizes = ['pequeno', 'mediano', 'grande'];

        foreach ($coords as $point) {
            if (! is_array($point)) {
                return back()->withErrors(['boulder' => 'Formato de coordenadas no válido.'])->withInput();
            }

            $x = $point['x'] ?? null;
            $y = $point['y'] ?? null;
            $type = $point['type'] ?? null;
            $size = $point['size'] ?? null;

            if (! is_numeric($x) || ! is_numeric($y)) {
                return back()->withErrors(['boulder' => 'Cada punto debe tener coordenadas numéricas.'])->withInput();
            }

            if ((float) $x < 0 || (float) $x > 100 || (float) $y < 0 || (float) $y > 100) {
                return back()->withErrors(['boulder' => 'Las coordenadas deben estar entre 0 y 100.'])->withInput();
            }

            if (! in_array($type, $validTypes, true) || ! in_array($size, $validSizes, true)) {
                return back()->withErrors(['boulder' => 'Cada punto debe incluir tipo y tamaño válidos.'])->withInput();
            }
        }

        KilterBlock::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'grade' => $data['grade'],
            'map_id' => (int) $data['map_id'],
            'user_id' => $request->user()->id,
            'boulder' => $data['boulder'],
        ]);

        return redirect()
            ->route('kilter')
            ->with('status', 'Bloque creado correctamente.');
    }

    public function storeMap(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
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
