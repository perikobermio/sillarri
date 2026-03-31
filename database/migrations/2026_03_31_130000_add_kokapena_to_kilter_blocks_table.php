<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('kilter_blocks', function (Blueprint $table): void {
            $table->string('kokapena', 120)->nullable()->after('grade');
        });
    }

    public function down(): void
    {
        Schema::table('kilter_blocks', function (Blueprint $table): void {
            $table->dropColumn('kokapena');
        });
    }
};
