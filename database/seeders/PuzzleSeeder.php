<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puzzle;
use App\Models\PuzzlePiece;

class PuzzleSeeder extends Seeder
{
    public function run(): void
    {
        if (Puzzle::count() > 0) return;

        $puzzle = Puzzle::create([
            'title' => 'Esports Championship (Minggu 1)',
            'image_path' => 'puzzles/pembda_puzzle_1.png',
            'grid_x' => 10,
            'grid_y' => 5,
            'is_active' => true,
        ]);

        $totalPieces = $puzzle->grid_x * $puzzle->grid_y;
        $pieces = [];
        for ($i = 0; $i < $totalPieces; $i++) {
            $pieces[] = [
                'puzzle_id' => $puzzle->id,
                'piece_index' => $i,
                'is_placed' => false,
                'placed_by_user_id' => null,
                'placed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        PuzzlePiece::insert($pieces);
    }
}
