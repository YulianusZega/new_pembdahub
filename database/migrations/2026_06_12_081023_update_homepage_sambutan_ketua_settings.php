<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newQuote = "Salam sejahtera, Ya'ahowu! Sebagai garda terdepan pendidikan di Kepulauan Nias, Yayasan Perguruan PEMBDA berkomitmen penuh melahirkan generasi emas yang tangguh, berkarakter mulia, dan unggul secara teknologi. Selaras dengan motto abadi kami: 'Keep Moving Forward / Maju Terus Pantang Mundur', kami terus berinovasi tanpa henti melalui PembdaHUB untuk menciptakan ekosistem pembelajaran digital terbaik. Bersama, kita langkah demi langkah melangkah pasti menjawab tantangan zaman demi masa depan Nias yang gemilang!";

        DB::table('settings')->updateOrInsert(
            ['key' => 'ketua_quote'],
            [
                'value' => $newQuote,
                'type' => 'string',
                'group' => 'homepage',
                'description' => 'Teks Sambutan Ketua Yayasan di Halaman Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $oldQuote = "Salam sejahtera, Ya'ahowu! Yayasan Perguruan PEMBDA Nias berkomitmen membangun generasi muda Nias yang berkualitas, berkarakter, dan siap menghadapi tantangan masa depan. Melalui tiga unit sekolah kami, kami menyediakan pendidikan yang bermutu dan terjangkau bagi masyarakat Nias.";

        DB::table('settings')->updateOrInsert(
            ['key' => 'ketua_quote'],
            [
                'value' => $oldQuote,
                'type' => 'string',
                'group' => 'homepage',
                'description' => 'Teks Sambutan Ketua Yayasan di Halaman Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
};
