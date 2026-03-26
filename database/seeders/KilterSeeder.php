<?php

namespace Database\Seeders;

use App\Models\KilterBlock;
use App\Models\KilterMap;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KilterSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'erik@sillarri.local'],
            [
                'name' => 'Periko',
                'username' => 'erik',
                'password' => Hash::make('erik'),
                'is_admin' => true,
            ]
        );

        $member = User::updateOrCreate(
            ['email' => 'gotzon@sillarri.local'],
            [
                'name' => 'Gotzon',
                'username' => 'gotzon',
                'password' => Hash::make('gotzon'),
                'is_admin' => false,
            ]
        );

        KilterBlock::query()->delete();
        KilterMap::query()->delete();

        $mainMap = KilterMap::create([
            'name' => 'Kilter Board 40º',
            'image' => 'kilter-main-40.jpg',
        ]);

        $trainingMap = KilterMap::create([
            'name' => 'Kilter Board 50º',
            'image' => 'kilter-training-50.jpg',
        ]);

        KilterBlock::insert([
            [
                'name' => 'Laia Power Start',
                'description' => 'Entrada de compresión y final a regleta lateral.',
                'grade' => '6B+',
                'map_id' => $mainMap->id,
                'user_id' => $admin->id,
                'boulder' => '[{"x":20.0,"y":18.0,"type":"comienzo","size":"mediano"},{"x":31.0,"y":29.0,"type":"mano_pie","size":"mediano"},{"x":45.0,"y":41.0,"type":"mano_pie","size":"grande"},{"x":58.0,"y":52.0,"type":"top","size":"grande"}]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Volcanic Pinch',
                'description' => 'Bloque explosivo de pinzas con salida dinámica.',
                'grade' => '6C',
                'map_id' => $mainMap->id,
                'user_id' => $admin->id,
                'boulder' => '[{"x":16.0,"y":23.0,"type":"comienzo","size":"pequeno"},{"x":24.0,"y":36.0,"type":"pie","size":"pequeno"},{"x":39.0,"y":47.0,"type":"mano_pie","size":"mediano"},{"x":55.0,"y":60.0,"type":"top","size":"grande"}]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basque Compression',
                'description' => 'Secuencia técnica de cuerpo entero en desplome.',
                'grade' => '7A',
                'map_id' => $trainingMap->id,
                'user_id' => $member->id,
                'boulder' => '[{"x":14.0,"y":25.0,"type":"comienzo","size":"mediano"},{"x":28.0,"y":38.0,"type":"mano_pie","size":"mediano"},{"x":42.0,"y":52.0,"type":"pie","size":"pequeno"},{"x":57.0,"y":67.0,"type":"top","size":"grande"}]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mendi Mantle',
                'description' => 'Cruce largo y salida de mantel con pie alto.',
                'grade' => '7A+',
                'map_id' => $trainingMap->id,
                'user_id' => $member->id,
                'boulder' => '[{"x":18.0,"y":19.0,"type":"comienzo","size":"mediano"},{"x":32.0,"y":31.0,"type":"mano_pie","size":"mediano"},{"x":46.0,"y":40.0,"type":"mano_pie","size":"grande"},{"x":62.0,"y":49.0,"type":"top","size":"grande"}]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kiskali Crimp Line',
                'description' => 'Regletas pequeñas con ritmo continuo y cierre duro.',
                'grade' => '7B',
                'map_id' => $mainMap->id,
                'user_id' => $admin->id,
                'boulder' => '[{"x":19.0,"y":27.0,"type":"comienzo","size":"mediano"},{"x":35.0,"y":40.0,"type":"pie","size":"pequeno"},{"x":49.0,"y":53.0,"type":"mano_pie","size":"mediano"},{"x":64.0,"y":66.0,"type":"top","size":"grande"}]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
