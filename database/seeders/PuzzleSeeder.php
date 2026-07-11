<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Puzzle;
use App\Models\PuzzlePiece;

class PuzzleSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan DB transaction + lock agar tidak bisa dieksekusi ganda (race condition)
        DB::transaction(function () {
            $puzzle = Puzzle::lockForUpdate()->first();

            if ($puzzle) {
                // Puzzle sudah ada, cek apakah perlu pasang 10 keping bonus
                $placedCount = PuzzlePiece::where('puzzle_id', $puzzle->id)
                    ->where('is_placed', true)
                    ->lockForUpdate()
                    ->count();

                if ($placedCount < 10) {
                    $needed = 10 - $placedCount;
                    // Ambil ID keping yang belum terpasang, lalu update satu per satu
                    $unplacedIds = PuzzlePiece::where('puzzle_id', $puzzle->id)
                        ->where('is_placed', false)
                        ->inRandomOrder()
                        ->limit($needed)
                        ->pluck('id');

                    PuzzlePiece::whereIn('id', $unplacedIds)->update([
                        'is_placed' => true,
                        'placed_at' => now(),
                        // placed_by_user_id null = Sistem (Bonus)
                    ]);
                }
                return;
            }

            // Buat puzzle baru
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

            // Pasang 10 keping acak sebagai bonus awal
            $bonusIds = PuzzlePiece::where('puzzle_id', $puzzle->id)
                ->inRandomOrder()
                ->limit(10)
                ->pluck('id');

            PuzzlePiece::whereIn('id', $bonusIds)->update([
                'is_placed' => true,
                'placed_at' => now(),
            ]);
        });
    }
}
