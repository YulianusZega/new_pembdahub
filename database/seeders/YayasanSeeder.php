<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class YayasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates a Ketua Yayasan user account linked to the Yayasan school record.
     */
    public function run(): void
    {
        // Find the yayasan school record
        $yayasan = School::where('type', 'yayasan')->first();

        if (!$yayasan) {
            $this->command->error('❌ Record Yayasan belum ada di tabel schools. Jalankan migration terlebih dahulu.');
            return;
        }

        $username = 'ketua_yayasan';
        $email = 'ketua.yayasan@pembdahub.sch.id';

        // Check if user already exists
        if (User::where('username', $username)->orWhere('email', $email)->exists()) {
            $this->command->warn('⚠️  Akun Ketua Yayasan sudah ada, dilewati.');
            return;
        }

        User::create([
            'name' => 'Ketua Yayasan PEMBDA',
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('KetuaYayasan@2026!'),
            'role' => 'ketua_yayasan',
            'school_id' => $yayasan->id,
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $this->command->info('✅ Berhasil membuat akun Ketua Yayasan');
        $this->command->info("📝 Username: {$username}");
        $this->command->info('🔑 Password default: KetuaYayasan@2026!');
        $this->command->info('⚠️  User wajib ubah password saat login pertama!');
    }
}
