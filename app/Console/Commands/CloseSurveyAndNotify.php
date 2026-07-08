<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Survey;
use App\Models\User;
use App\Models\School;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class CloseSurveyAndNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surveys:close-and-notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close surveys that have passed end_date by 10 mins and send WA notification';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $waService)
    {
        // 1. Cari survei aktif yang end_date-nya sudah lewat 10 menit
        $surveys = Survey::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->subMinutes(10))
            ->get();

        if ($surveys->isEmpty()) {
            $this->info('No surveys to close and notify.');
            return;
        }

        foreach ($surveys as $survey) {
            $this->info("Processing Survey ID: {$survey->id}");
            
            // Tutup survei
            $survey->update(['status' => 'closed']);
            
            // 2. Kumpulkan Rekapitulasi Survey (Top Score)
            $responses = $survey->responses()->with(['user', 'answers.question'])->get();
            
            foreach ($responses as $response) {
                $sum = 0;
                $count = 0;
                foreach ($response->answers as $answer) {
                    if ($answer->question) {
                        if ($answer->question->type === 'scale' && !is_null($answer->rating)) {
                            if ($answer->question->scale_type !== 'yes_no') {
                                $sum += $answer->rating;
                                $count++;
                            }
                        } elseif ($answer->question->type === 'text' && !is_null($answer->essay_score)) {
                            $sum += $answer->essay_score;
                            $count++;
                        }
                    }
                }
                $response->average_score = $count > 0 ? round($sum / $count, 2) : 0;
            }

            // Urutkan dari tertinggi dan ambil top (atau semua, sesuai permintaan "dikirim semua namun sederhana")
            $responses = $responses->sortByDesc('average_score')->values();

            // Bangun text WA
            $schoolName = $survey->school ? $survey->school->name : 'Global';
            $message = "*Rekapitulasi Survey {$survey->title} - {$schoolName}*\n\n";
            $message .= "Berikut adalah hasil penilaian responden:\n\n";
            
            foreach ($responses as $index => $res) {
                $no = $index + 1;
                $name = $res->user ? $res->user->name : 'Anonim';
                $score = $res->average_score;
                
                // Menentukan Kategori Individu
                $kategori = '';
                if ($score >= 4.0) {
                    $kategori = "Sangat Baik (Kompetensi sangat memadai)";
                } elseif ($score >= 3.0) {
                    $kategori = "Baik (Memenuhi standar, namun bisa ditingkatkan)";
                } elseif ($score >= 2.0) {
                    $kategori = "Cukup (Membutuhkan evaluasi & pembinaan)";
                } else {
                    $kategori = "Kurang (Sangat membutuhkan pendampingan khusus)";
                }

                $message .= "{$no}. *{$name}*\n     Skor: {$score} / 5.00\n     Kategori: {$kategori}\n\n";
            }
            
            // Kesimpulan akhir
            $message .= "*Kesimpulan Akhir*\n";
            if ($responses->isEmpty()) {
                $message .= "Belum ada responden yang mengisi survei ini hingga batas waktu ditutup.\n";
            } else {
                $avgAll = round($responses->avg('average_score'), 2);
                if ($avgAll >= 4.0) {
                    $message .= "Secara keseluruhan hasil survei Sangat Baik (Rata-rata: {$avgAll}).\n";
                } elseif ($avgAll >= 3.0) {
                    $message .= "Secara keseluruhan hasil survei Baik (Rata-rata: {$avgAll}).\n";
                } else {
                    $message .= "Secara keseluruhan hasil survei perlu mendapat perhatian (Rata-rata: {$avgAll}).\n";
                }
            }
            
            $message .= "\nUntuk melihat detail, rekomendasi AI individu, dan jawaban essay, silakan login ke PembdaHUB | www.perguruanpembda.com";

            // 3. Kirim WA ke Ketua Yayasan
            // Ketua Yayasan ada di tabel users, role 'ketua_yayasan'
            $ketuaYayasanUsers = User::where('role', 'ketua_yayasan')->get();
            $recipients = [];
            
            foreach ($ketuaYayasanUsers as $ky) {
                $phone = null;
                if ($ky->teacher && $ky->teacher->phone) {
                    $phone = $ky->teacher->phone;
                } elseif ($ky->employee && $ky->employee->phone) {
                    $phone = $ky->employee->phone;
                }
                
                if ($phone) {
                    $recipients[] = ['phone' => $phone, 'message' => $message];
                }
            }
            
            // 4. Kirim WA ke Kepala Sekolah Unit Tersebut
            if ($survey->school_id) {
                $school = School::find($survey->school_id);
                if ($school && $school->principal_id) {
                    $principal = \App\Models\Teacher::find($school->principal_id);
                    if ($principal && $principal->phone) {
                        $recipients[] = ['phone' => $principal->phone, 'message' => $message];
                    }
                }
            }
            
            // Hilangkan duplikat jika ada
            $recipients = collect($recipients)->unique('phone')->values()->all();
            
            if (count($recipients) > 0) {
                $waService->sendBulk($recipients);
                $this->info("Sent WA to " . count($recipients) . " recipients for survey {$survey->id}");
                Log::info("Survey {$survey->id} closed automatically and WA sent to " . count($recipients) . " recipients.");
            } else {
                $this->warn("Survey {$survey->id} closed but NO phone numbers found for Ketua Yayasan or Kepala Sekolah.");
                Log::warning("Survey {$survey->id} closed but NO phone numbers found for Ketua Yayasan or Kepala Sekolah.");
            }
        }
    }
}
