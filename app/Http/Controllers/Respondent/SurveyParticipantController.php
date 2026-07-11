<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyParticipantController extends Controller
{
    /**
     * Determine the respondent role based on user role.
     */
    private function getRespondentRole()
    {
        $user = auth()->user();
        $activeRole = session('active_role', $user->role);
        
        if ($activeRole === 'guru' || $user->isGuru()) {
            return 'guru';
        } elseif ($activeRole === 'siswa' || $user->isSiswa()) {
            return 'siswa';
        }
        
        abort(403, 'Hanya Guru dan Siswa yang dapat berpartisipasi dalam survei.');
    }

    /**
     * List all active surveys targeted to the user.
     */
    public function index()
    {
        $user = auth()->user();
        $role = $this->getRespondentRole();

        // Fetch active surveys matching user's role and school unit
        // Excluding surveys they have already submitted responses for
        $now = now();
        $surveys = Survey::where('status', 'active')
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->whereIn('target_respondent', [$role, 'semua'])
            ->where(function ($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereNull('school_id');
            })
            ->whereDoesntHave('responses', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch completed surveys history
        $completedSurveys = Survey::whereIn('target_respondent', [$role, 'semua'])
            ->where(function ($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereNull('school_id');
            })
            ->whereHas('responses', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->withCount('questions')
            ->with(['responses' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'available' => $surveys->count(),
            'completed' => $completedSurveys->count(),
            'total' => $surveys->count() + $completedSurveys->count(),
        ];

        return view('respondent.surveys.index', compact('surveys', 'completedSurveys', 'stats', 'role'));
    }

    /**
     * Show survey questions form.
     */
    public function take(Survey $survey)
    {
        $user = auth()->user();
        $role = $this->getRespondentRole();
        $now = now();

        // Validation checks
        if ($survey->status !== 'active') {
            abort(404, 'Survei ini tidak aktif.');
        }
        if ($survey->start_date && $survey->start_date > $now) {
            abort(403, 'Survei ini belum dimulai (Waktu buka: ' . $survey->start_date->format('d-m-Y H:i') . ').');
        }
        if ($survey->end_date && $survey->end_date < $now) {
            abort(403, 'Survei ini sudah melewati batas waktu (Waktu tutup: ' . $survey->end_date->format('d-m-Y H:i') . ').');
        }

        if (!in_array($survey->target_respondent, [$role, 'semua'])) {
            abort(403, 'Survei ini tidak ditargetkan untuk Anda.');
        }

        if ($survey->school_id && $survey->school_id !== $user->school_id) {
            abort(403, 'Survei ini untuk unit sekolah lain.');
        }

        // Check if already completed
        $hasSubmitted = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($hasSubmitted) {
            return redirect()->route($role . '.surveys.index')
                ->with('error', 'Anda telah menyelesaikan survei ini.');
        }

        $questions = $survey->questions()->get();

        return view('respondent.surveys.take', compact('survey', 'questions', 'role'));
    }

    /**
     * Submit responses.
     */
    public function submit(Request $request, Survey $survey)
    {
        $user = auth()->user();
        $role = $this->getRespondentRole();
        $now = now();

        // Validation checks
        if ($survey->status !== 'active') {
            abort(404, 'Survei ini tidak aktif.');
        }
        if ($survey->start_date && $survey->start_date > $now) {
            abort(403, 'Survei ini belum dimulai.');
        }
        if ($survey->end_date && $survey->end_date < $now) {
            abort(403, 'Survei ini sudah melewati batas waktu dan ditutup otomatis.');
        }

        if (!in_array($survey->target_respondent, [$role, 'semua'])) {
            abort(403, 'Survei ini tidak ditargetkan untuk Anda.');
        }

        if ($survey->school_id && $survey->school_id !== $user->school_id) {
            abort(403, 'Survei ini untuk unit sekolah lain.');
        }

        // Check if already completed
        $hasSubmitted = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($hasSubmitted) {
            return redirect()->route($role . '.surveys.index')
                ->with('error', 'Anda telah menyelesaikan survei ini.');
        }

        $teacherType = null;
        if ($role === 'guru') {
            $request->validate([
                'teacher_type' => 'required|in:kejuruan,umum',
            ], [
                'teacher_type.required' => 'Anda wajib memilih tipe guru Anda terlebih dahulu.',
                'teacher_type.in' => 'Tipe guru yang dipilih tidak valid.',
            ]);
            $teacherType = $request->input('teacher_type');
        }

        $questions = $survey->questions()->get();
        $rules = [];
        $messages = [];

        foreach ($questions as $q) {
            // Lewati validasi jika pertanyaan tidak ditargetkan untuk tipe guru yang bersangkutan
            if ($role === 'guru' && !empty($q->target_guru) && $q->target_guru !== $teacherType) {
                continue;
            }

            if ($q->type === 'scale') {
                $scaleType = $q->scale_type ?? 'likert_5';
                if ($scaleType === 'yes_no') {
                    $rules['answers.' . $q->id] = 'required|integer|in:0,1';
                    $messages['answers.' . $q->id . '.required'] = 'Pertanyaan wajib dijawab.';
                    $messages['answers.' . $q->id . '.in'] = 'Jawaban harus berupa Ya atau Tidak.';
                } elseif ($scaleType === 'likert_4') {
                    $rules['answers.' . $q->id] = 'required|integer|between:1,4';
                    $messages['answers.' . $q->id . '.required'] = 'Pertanyaan wajib dinilai.';
                    $messages['answers.' . $q->id . '.between'] = 'Penilaian harus berada dalam skala 1 sampai 4.';
                } else {
                    $rules['answers.' . $q->id] = 'required|integer|between:1,5';
                    $messages['answers.' . $q->id . '.required'] = 'Pertanyaan wajib dinilai.';
                    $messages['answers.' . $q->id . '.between'] = 'Penilaian harus berada dalam skala 1 sampai 5.';
                }
            } else {
                $rules['answers.' . $q->id] = 'nullable|string';
            }
        }

        $request->validate($rules, $messages);

        // Save responses in database transaction
        DB::transaction(function () use ($survey, $user, $questions, $request, $teacherType, $role) {
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'user_id' => $user->id,
                'school_id' => $user->school_id,
                'teacher_type' => $teacherType,
            ]);

            $answers = $request->input('answers', []);

            foreach ($questions as $q) {
                // Lewati penyimpanan jika pertanyaan tidak ditargetkan untuk tipe guru yang bersangkutan
                if ($role === 'guru' && !empty($q->target_guru) && $q->target_guru !== $teacherType) {
                    continue;
                }

                $val = $answers[$q->id] ?? null;

                if ($q->type === 'scale' && is_null($val)) {
                    continue;
                }

                $data = [
                    'response_id' => $response->id,
                    'question_id' => $q->id,
                ];

                if ($q->type === 'scale') {
                    $data['rating'] = intval($val);
                } else {
                    $data['answer_text'] = $val;
                }

                DB::table('survey_answers')->insert($data);
            }
        });

        return redirect()->route($role . '.surveys.index')
            ->with('success', 'Terima kasih atas partisipasi Anda. Jawaban survei berhasil dikirim.');
    }
}
