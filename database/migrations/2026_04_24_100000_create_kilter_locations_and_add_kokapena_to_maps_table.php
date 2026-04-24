<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kilter_locations', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->unique();
            $table->timestamps();
        });

        Schema::table('kilter_maps', function (Blueprint $table): void {
            $table->string('kokapena', 120)->nullable()->after('name');
        });

        $now = now();

        $locationNames = DB::table('kilter_blocks')
            ->whereNotNull('kokapena')
            ->where('kokapena', '!=', '')
            ->distinct()
            ->orderBy('kokapena')
            ->pluck('kokapena')
            ->filter(static fn ($name) => is_string($name) && trim($name) !== '')
            ->map(static fn ($name) => trim((string) $name))
            ->unique()
            ->values();

        foreach ($locationNames as $name) {
            DB::table('kilter_locations')->updateOrInsert(
                ['name' => $name],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        $mapLocations = DB::table('kilter_blocks')
            ->select('map_id', 'kokapena', DB::raw('count(*) as total'))
            ->whereNotNull('map_id')
            ->whereNotNull('kokapena')
            ->where('kokapena', '!=', '')
            ->groupBy('map_id', 'kokapena')
            ->orderBy('map_id')
            ->orderByDesc('total')
            ->get()
            ->groupBy('map_id');

        foreach ($mapLocations as $mapId => $rows) {
            $bestMatch = collect($rows)->sortByDesc('total')->first();
            $locationName = is_object($bestMatch) ? trim((string) ($bestMatch->kokapena ?? '')) : '';
            if ($locationName === '') {
                continue;
            }

            DB::table('kilter_maps')
                ->where('id', $mapId)
                ->update(['kokapena' => $locationName]);
        }
    }

    public function down(): void
    {
        Schema::table('kilter_maps', function (Blueprint $table): void {
            $table->dropColumn('kokapena');
        });

        Schema::dropIfExists('kilter_locations');
    }
};
