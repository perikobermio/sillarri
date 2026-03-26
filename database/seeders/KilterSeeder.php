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
                'boulder' => '[(120,82),(166,140),(244,195),(311,228)]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Volcanic Pinch',
                'description' => 'Bloque explosivo de pinzas con salida dinámica.',
                'grade' => '6C',
                'map_id' => $mainMap->id,
                'user_id' => $admin->id,
                'boulder' => '[(88,102),(121,164),(208,211),(290,255)]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basque Compression',
                'description' => 'Secuencia técnica de cuerpo entero en desplome.',
                'grade' => '7A',
                'map_id' => $trainingMap->id,
                'user_id' => $member->id,
                'boulder' => '[(70,118),(133,166),(201,224),(276,280)]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mendi Mantle',
                'description' => 'Cruce largo y salida de mantel con pie alto.',
                'grade' => '7A+',
                'map_id' => $trainingMap->id,
                'user_id' => $member->id,
                'boulder' => '[(94,88),(162,140),(230,172),(318,210)]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kiskali Crimp Line',
                'description' => 'Regletas pequeñas con ritmo continuo y cierre duro.',
                'grade' => '7B',
                'map_id' => $mainMap->id,
                'user_id' => $admin->id,
                'boulder' => '[(105,124),(180,175),(252,220),(330,269)]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
