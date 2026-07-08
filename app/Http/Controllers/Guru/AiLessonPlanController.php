<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiLessonPlanController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Show RPP form.
     */
    public function index()
    {
        return view('guru.ai.lesson_plan');
    }

    /**
     * Generate RPP using Gemini.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'school_type' => 'required|string|in:SMP,SMA,SMK',
            'grade_level' => 'required|string',
            'subject'     => 'required|string|max:255',
            'topic'       => 'required|string|max:255',
            'objectives'  => 'required|string|max:1000',
            'duration'    => 'required|string|max:100',
        ]);

        $prompt = "Buatkan Modul Ajar RPP Kurikulum Merdeka yang sangat lengkap dan profesional untuk sekolah tingkat: " . $request->school_type . "\n" .
                  "Kelas: " . $request->grade_level . "\n" .
                  "Mata Pelajaran: " . $request->subject . "\n" .
                  "Tema/Topik: " . $request->topic . "\n" .
                  "Target Capaian/Tujuan Pembelajaran: " . $request->objectives . "\n" .
                  "Alokasi Waktu: " . $request->duration . "\n\n" .
                  "Format output HARUS menggunakan Markdown bahasa Indonesia yang rapi dengan heading (#, ##, ###) dan mencakup:\n" .
                  "1. Informasi Umum (Mata Pelajaran, Kelas, Alokasi Waktu, Profil Pelajar Pancasila yang dikembangkan)\n" .
                  "2. Komponen Inti (Tujuan Pembelajaran, Pemahaman Bermakna, Pertanyaan Pemantik)\n" .
                  "3. Kegiatan Pembelajaran (Pendahuluan, Inti, Penutup secara mendalam)\n" .
                  "4. Asesmen & Penilaian (Formatif, Sumatif, beserta rubrik singkat jika memungkinkan).\n" .
                  "Jangan menyertakan kata pembuka atau penutup tambahan lainnya. Mulai langsung dari judul utama.";

        $markdown = $this->gemini->generateText($prompt);

        return response()->json([
            'success'  => true,
            'markdown' => $markdown,
        ]);
    }

    /**
     * Export generated Markdown to MS Word document.
     */
    public function download(Request $request)
    {
        $request->validate([
            'subject'          => 'required|string',
            'topic'            => 'required|string',
            'markdown_content' => 'required|string',
        ]);

        $subject = $request->input('subject');
        $topic = $request->input('topic');
        $markdown = $request->input('markdown_content');

        $filename = 'Modul_Ajar_' . Str::slug($subject) . '_' . Str::slug($topic) . '_' . date('Ymd') . '.doc';

        $headers = [
            "Content-type"        => "application/msword",
            "Content-Disposition" => "attachment;Filename=" . $filename,
            "Pragma"              => "no-cache",
            "Expires"             => "0",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0"
        ];

        // Format Markdown content into simple HTML Word-compliant tags
        $htmlContent = nl2br(e($markdown));
        
        // Convert Markdown headers
        $htmlContent = preg_replace('/^#\s+(.*)$/m', '<h1>$1</h1>', $htmlContent);
        $htmlContent = preg_replace('/^##\s+(.*)$/m', '<h2>$1</h2>', $htmlContent);
        $htmlContent = preg_replace('/^###\s+(.*)$/m', '<h3>$1</h3>', $htmlContent);
        
        // Convert Bold and Italic
        $htmlContent = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $htmlContent);
        $htmlContent = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $htmlContent);
        
        // Convert Bullet points
        $htmlContent = preg_replace('/^\*\s+(.*)$/m', '<li>$1</li>', $htmlContent);

        $wordTemplate = "
        <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
        <head>
            <title>Modul Ajar</title>
            <!--[if gte mso 9]>
            <xml>
                <w:WordDocument>
                    <w:View>Print</w:View>
                    <w:Zoom>100</w:Zoom>
                    <w:DoNotOptimizeForBrowser/>
                </w:WordDocument>
            </xml>
            <![endif]-->
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    font-size: 11pt;
                    line-height: 1.5;
                }
                h1 {
                    font-size: 18pt;
                    color: #1e1b4b;
                    margin-top: 12pt;
                    margin-bottom: 6pt;
                    border-bottom: 2px solid #4f2ed1;
                    padding-bottom: 3pt;
                }
                h2 {
                    font-size: 14pt;
                    color: #3730a3;
                    margin-top: 12pt;
                    margin-bottom: 4pt;
                }
                h3 {
                    font-size: 12pt;
                    color: #4f2ed1;
                    margin-top: 10pt;
                    margin-bottom: 2pt;
                }
                li {
                    margin-bottom: 4pt;
                }
            </style>
        </head>
        <body>
            {$htmlContent}
        </body>
        </html>";

        return response($wordTemplate, 200, $headers);
    }
}
