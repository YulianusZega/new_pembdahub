<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomepageContentController extends Controller
{
    public function index()
    {
        $settings = [
            // Statistik
            'stat_tahun' => Setting::getValue('stat_tahun', '1970'),
            'stat_siswa' => Setting::getValue('stat_siswa', '1700'),
            'stat_unit' => Setting::getValue('stat_unit', '3'),
            'stat_program' => Setting::getValue('stat_program', '5'),
            
            // Sambutan Ketua
            'ketua_nama' => Setting::getValue('ketua_nama', "Yulianus Zega, S.Kom"),
            'ketua_jabatan' => Setting::getValue('ketua_jabatan', "Ketua Yayasan Perguruan PEMBDA Nias"),
            'ketua_quote' => Setting::getValue('ketua_quote', "Salam sejahtera, Ya'ahowu! Sebagai garda terdepan pendidikan di Kepulauan Nias, Yayasan Perguruan PEMBDA berkomitmen penuh melahirkan generasi emas yang tangguh, berkarakter mulia, dan unggul secara teknologi. Selaras dengan motto abadi kami: 'Keep Moving Forward / Maju Terus Pantang Mundur', kami terus berinovasi tanpa henti melalui PembdaHUB untuk menciptakan ekosistem pembelajaran digital terbaik. Bersama, kita langkah demi langkah melangkah pasti menjawab tantangan zaman demi masa depan Nias yang gemilang!"),
            
            // PSB / Pendaftaran
            'psb_tp' => Setting::getValue('psb_tp', '2026/2027'),
            'psb_periode' => Setting::getValue('psb_periode', '1 Feb – 30 Jun 2026'),
            'psb_status' => Setting::getValue('psb_status', 'Dibuka'),
        ];

        return view('admin.homepage.content', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Statistik
            'stat_tahun' => 'required|string',
            'stat_siswa' => 'required|string',
            'stat_unit' => 'required|string',
            'stat_program' => 'required|string',
            
            // Sambutan Ketua
            'ketua_nama' => 'required|string|max:255',
            'ketua_jabatan' => 'required|string|max:255',
            'ketua_quote' => 'required|string',
            
            // PSB
            'psb_tp' => 'required|string|max:50',
            'psb_periode' => 'required|string|max:100',
            'psb_status' => 'required|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value, 'string', 'homepage');
        }

        return redirect()->route('admin.homepage-content.index')
            ->with('success', 'Konten umum homepage berhasil diperbarui!');
    }
}
