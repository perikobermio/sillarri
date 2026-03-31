<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('kilter_block_recotations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kilter_block_id')->constrained('kilter_blocks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('grade', 20);
            $table->timestamps();
            $table->unique(['kilter_block_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kilter_block_recotations');
    }
};
