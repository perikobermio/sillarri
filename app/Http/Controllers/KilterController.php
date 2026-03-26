<?php

namespace App\Http\Controllers;

use App\Models\KilterBlock;
use App\Models\KilterMap;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KilterController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $blocks = KilterBlock::query()
            ->with(['map', 'creator'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->get();

        return view('kilter.index', [
            'blocks' => $blocks,
            'search' => $search,
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
            'boulder' => ['required', 'string', 'max:500'],
        ]);

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

        $path = $request->file('image')->store('kilter_maps', 'public');
        $physicalPath = Storage::disk('public')->path($path);

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
        ]);
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
