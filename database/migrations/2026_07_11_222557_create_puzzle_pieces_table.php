<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puzzle_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puzzle_id')->constrained()->onDelete('cascade');
            $table->integer('piece_index'); // 0 to (grid_x * grid_y - 1)
            $table->boolean('is_placed')->default(false);
            $table->foreignId('placed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->unique(['puzzle_id', 'piece_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puzzle_pieces');
    }
};
