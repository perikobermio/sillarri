<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\Finder\SplFileInfo;

class EskaladaTxosneroaController extends Controller
{
    private const PUBLIC_BASE_PATH = 'media/eskalada-txosneroa';

    public function index(): View
    {
        $config = config('eskalada_txosneroa', []);
        $mediaByYear = $this->mediaByYear();
        $years = collect($config['years'] ?? [])
            ->keys()
            ->merge($mediaByYear->keys())
            ->unique()
            ->sortDesc()
            ->values()
            ->map(function (string $year) use ($config, $mediaByYear): array {
                $meta = $config['years'][$year] ?? [];
                $media = $mediaByYear->get($year, collect())->values();

                return [
                    'year' => $year,
                    'headline' => $meta['headline'] ?? $year.'ko edizioa',
                    'summary' => $meta['summary'] ?? null,
                    'winners' => $meta['winners'] ?? [],
                    'images' => $media->where('type', 'image')->values()->all(),
                    'videos' => $media->where('type', 'video')->values()->all(),
                ];
            })
            ->all();

        return view('eskalada-txosneroa.index', [
            'event' => [
                'eyebrow' => $config['eyebrow'] ?? 'Urteko ekitaldia',
                'title' => $config['title'] ?? 'Eskalada Txosneroa',
                'intro' => $config['intro'] ?? '',
                'download_note' => $config['download_note'] ?? '',
            ],
            'years' => $years,
        ]);
    }

    private function mediaByYear()
    {
        $basePath = public_path(self::PUBLIC_BASE_PATH);

        if (! File::isDirectory($basePath)) {
            return collect();
        }

        return collect(File::directories($basePath))
            ->mapWithKeys(function (string $directory): array {
                $year = basename($directory);
                $media = collect(File::allFiles($directory))
                    ->map(fn (SplFileInfo $file): ?array => $this->mapMediaFile($year, $file))
                    ->filter()
                    ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values();

                return [$year => $media];
            });
    }

    private function mapMediaFile(string $year, SplFileInfo $file): ?array
    {
        $extension = strtolower($file->getExtension());
        $type = match (true) {
            in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) => 'image',
            in_array($extension, ['mp4', 'webm', 'mov', 'm4v'], true) => 'video',
            default => null,
        };

        if ($type === null) {
            return null;
        }

        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());
        $publicPath = self::PUBLIC_BASE_PATH.'/'.$year.'/'.$relativePath;
        $filename = pathinfo($relativePath, PATHINFO_FILENAME);

        return [
            'type' => $type,
            'title' => Str::of($filename)->replace(['-', '_'], ' ')->squish()->title()->toString(),
            'url' => $this->publicUrl($publicPath),
            'download_name' => basename($relativePath),
            'extension' => strtoupper($extension),
            'size' => $this->formatBytes($file->getSize()),
        ];
    }

    private function publicUrl(string $path): string
    {
        $segments = array_map('rawurlencode', explode('/', trim($path, '/')));

        return '/'.implode('/', $segments);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB'];
        $value = $bytes / 1024;

        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'GB') {
                return number_format($value, $value >= 100 ? 0 : 1, ',', '.').' '.$unit;
            }

            $value /= 1024;
        }

        return $bytes.' B';
    }
}
