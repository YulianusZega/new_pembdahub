<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use App\Models\Teacher;
use App\Models\CbtQuestionBank;
use App\Models\CbtQuestion;
use App\Models\CbtQuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiQuestionGeneratorController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Display CBT AI Question Generator form.
     */
    public function index()
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        if (!$teacher) {
            abort(403, 'Akses ditolak: Data pendidik guru tidak ditemukan.');
        }

        $questionBanks = CbtQuestionBank::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->get();

        return view('guru.ai.question_generator', compact('questionBanks'));
    }

    /**
     * Parse input and call Gemini to generate draft questions.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'question_bank_id' => 'required|exists:cbt_question_banks,id',
            'content_type'     => 'required|string|in:text,pdf',
            'raw_text'         => 'required_if:content_type,text|nullable|string',
            'pdf_file'         => 'required_if:content_type,pdf|nullable|file|mimes:pdf|max:5120', // max 5MB
            'num_questions'    => 'required|integer|min:1|max:30',
            'difficulty'       => 'required|string|in:Easy,Medium,Hard',
        ]);

        $text = '';

        if ($request->content_type === 'pdf') {
            try {
                $file = $request->file('pdf_file');
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();
            } catch (\Exception $e) {
                Log::error('PDF parsing error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengekstrak konten teks dari file PDF: ' . $e->getMessage()
                ], 422);
            }
        } else {
            $text = $request->input('raw_text');
        }

        // Clean and trim text
        $text = trim(preg_replace('/\s+/', ' ', $text));
        
        if (empty($text)) {
            return response()->json([
                'success' => false,
                'message' => 'Teks materi kosong. Harap berikan teks atau unggah file PDF yang berisi tulisan.'
            ], 422);
        }

        // Limit content to prevent token limits (approx 80k chars)
        $text = substr($text, 0, 80000);

        $prompt = "Berdasarkan materi ajar berikut, buatlah sebanyak " . $request->num_questions . " soal pilihan ganda (A, B, C, D, E) dengan tingkat kesulitan: " . $request->difficulty . ".\n\n" .
                  "Materi Ajar:\n" . $text . "\n\n" .
                  "Format output HARUS berupa JSON Array valid berbahasa Indonesia, tanpa kata pembuka atau penjelasan di luar JSON. Setiap objek soal dalam JSON Array harus memiliki format berikut:\n" .
                  "[\n" .
                  "  {\n" .
                  "    \"question\": \"Teks pertanyaan di sini\",\n" .
                  "    \"options\": {\n" .
                  "      \"A\": \"Teks pilihan A\",\n" .
                  "      \"B\": \"Teks pilihan B\",\n" .
                  "      \"C\": \"Teks pilihan C\",\n" .
                  "      \"D\": \"Teks pilihan D\",\n" .
                  "      \"E\": \"Teks pilihan E\"\n" .
                  "    },\n" .
                  "    \"answer\": \"A\",\n" .
                  "    \"explanation\": \"Penjelasan singkat mengapa jawaban tersebut benar\"\n" .
                  "  }\n" .
                  "]\n" .
                  "Pastikan key 'answer' bernilai huruf kapital tunggal ('A', 'B', 'C', 'D', atau 'E').";

        try {
            $questions = $this->gemini->generateJson($prompt);

            // Double check data validity
            if (empty($questions) || !is_array($questions)) {
                throw new \Exception("Gemini returned invalid or empty question array.");
            }

            return response()->json([
                'success'   => true,
                'questions' => $questions,
            ]);
        } catch (\Exception $e) {
            Log::error('Gemini question generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal merumuskan soal menggunakan AI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save reviewed and approved questions to DB.
     */
    public function save(Request $request)
    {
        $request->validate([
            'question_bank_id' => 'required|exists:cbt_question_banks,id',
            'questions'        => 'required|array',
            'questions.*.question'    => 'required|string',
            'questions.*.options'     => 'required|array|min:2',
            'questions.*.answer'      => 'required|string|in:A,B,C,D,E',
            'questions.*.explanation' => 'nullable|string',
        ]);

        $bankId = $request->question_bank_id;
        $questions = $request->questions;

        try {
            DB::transaction(function () use ($bankId, $questions) {
                foreach ($questions as $qData) {
                    // Create CBT question
                    $question = CbtQuestion::create([
                        'question_bank_id' => $bankId,
                        'question_type'    => 'multiple_choice',
                        'question_text'    => $qData['question'],
                        'explanation'      => $qData['explanation'] ?? null,
                        'points'           => 1, // default score per question
                        'difficulty'       => 'sedang',
                        'answer_key'       => $qData['answer'],
                        'is_active'        => true,
                    ]);

                    // Create choices A-E
                    $sortOrder = 1;
                    foreach ($qData['options'] as $label => $textOption) {
                        CbtQuestionOption::create([
                            'question_id'  => $question->id,
                            'option_label' => $label,
                            'option_text'  => $textOption,
                            'is_correct'   => ($label === $qData['answer']),
                            'sort_order'   => $sortOrder++,
                        ]);
                    }
                }

                // Update question bank counter
                $bank = CbtQuestionBank::find($bankId);
                if ($bank) {
                    $bank->total_questions = $bank->questions()->count();
                    $bank->save();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan ' . count($questions) . ' soal ke dalam Bank Soal.'
            ]);
        } catch (\Exception $e) {
            Log::error('Saving generated questions failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan soal ke database: ' . $e->getMessage()
            ], 500);
        }
    }
}
