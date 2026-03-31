<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $count = DB::table('weather_locations')->count();
        if ($count > 0) {
            return;
        }

        $now = now();
        DB::table('weather_locations')->insert([
            ['name' => 'Ereño', 'label' => 'Ereño', 'lat' => 43.357, 'lon' => -2.625, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Urkiola', 'label' => 'Urkiola', 'lat' => 43.103, 'lon' => -2.646, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mañaria', 'label' => 'Mañaria', 'lat' => 43.137, 'lon' => -2.661, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Atauri', 'label' => 'Atauri', 'lat' => 42.736, 'lon' => -2.455, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gernika', 'label' => 'Gernika', 'lat' => 43.317, 'lon' => -2.678, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Turtzioz', 'label' => 'Turtzioz', 'lat' => 43.272, 'lon' => -3.255, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ramales', 'label' => 'Ramales', 'lat' => 43.257, 'lon' => -3.465, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('weather_locations')
            ->whereIn('name', ['Ereño', 'Urkiola', 'Mañaria', 'Atauri', 'Gernika', 'Turtzioz', 'Ramales'])
            ->delete();
    }
};
