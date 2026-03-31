<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weather_locations', function (Blueprint $table) {
            $table->string('label')->nullable()->after('name');
        });

        DB::table('weather_locations')->update([
            'label' => DB::raw('name'),
        ]);
    }

    public function down(): void
    {
        Schema::table('weather_locations', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
};
