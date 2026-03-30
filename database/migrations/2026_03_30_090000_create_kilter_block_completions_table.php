<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kilter_block_completions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kilter_block_id')->constrained('kilter_blocks')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'kilter_block_id'], 'kilter_block_completion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kilter_block_completions');
    }
};

