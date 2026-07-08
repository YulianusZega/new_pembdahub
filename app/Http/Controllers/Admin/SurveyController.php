<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\School;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    /**
     * Check authorization for managing a specific survey.
     */
    private function authorizeSurvey(Survey $survey)
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return;
        }

        if ($survey->school_id !== $user->school_id) {
            abort(403, 'Anda tidak memiliki akses ke survei ini.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Survey::with('school')->withCount('responses');

        // Filter by school if not super admin
        if (!$user->isSuperAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereNull('school_id');
            });
        }

        // Apply filters
        if ($request->filled('school_id') && $user->isSuperAdmin()) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('target_respondent')) {
            $query->where('target_respondent', $request->target_respondent);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $surveys = $query->orderBy('created_at', 'desc')->paginate(10);
        $schools = School::where('type', '!=', 'YAYASAN')->get();

        // Calculate summary stats
        $statsQuery = Survey::query();
        if (!$user->isSuperAdmin()) {
            $statsQuery->where('school_id', $user->school_id);
        }
        
        $stats = [
            'total' => $statsQuery->count(),
            'active' => (clone $statsQuery)->where('status', 'active')->count(),
            'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
            'closed' => (clone $statsQuery)->where('status', 'closed')->count(),
        ];

        return view('admin.surveys.index', compact('surveys', 'schools', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $schools = School::where('type', '!=', 'YAYASAN')->get();
        return view('admin.surveys.create', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_respondent' => 'required|string|in:guru,siswa,semua',
            'status' => 'required|string|in:draft,active,closed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'school_id' => $user->isSuperAdmin() ? 'nullable|exists:schools,id' : 'nullable',
        ]);

        $data = $request->only(['title', 'description', 'target_respondent', 'status', 'start_date', 'end_date']);
        $data['school_id'] = $user->isSuperAdmin() ? $request->school_id : $user->school_id;

        Survey::create($data);

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survei berhasil dibuat. Silakan tambahkan pertanyaan untuk survei ini.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Survey $survey)
    {
        $this->authorizeSurvey($survey);
        $schools = School::where('type', '!=', 'YAYASAN')->get();
        return view('admin.surveys.edit', compact('survey', 'schools'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Survey $survey)
    {
        $this->authorizeSurvey($survey);
        $user = auth()->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_respondent' => 'required|string|in:guru,siswa,semua',
            'status' => 'required|string|in:draft,active,closed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'school_id' => $user->isSuperAdmin() ? 'nullable|exists:schools,id' : 'nullable',
        ]);

        $data = $request->only(['title', 'description', 'target_respondent', 'status', 'start_date', 'end_date']);
        if ($user->isSuperAdmin()) {
            $data['school_id'] = $request->school_id;
        }

        $survey->update($data);

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survei berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey)
    {
        $this->authorizeSurvey($survey);
        $survey->delete();

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survei berhasil dihapus.');
    }

    /**
     * Show the form for managing questions.
     */
    public function questions(Survey $survey)
    {
        $this->authorizeSurvey($survey);
        $questions = $survey->questions()->get();
        return view('admin.surveys.questions', compact('survey', 'questions'));
    }

    /**
     * Store a newly created question in database.
     */
    public function storeQuestion(Request $request, Survey $survey)
    {
        $this->authorizeSurvey($survey);
        
        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|string|in:scale,text',
            'scale_type' => 'required_if:type,scale|nullable|string|in:likert_5,likert_4,competence_5,yes_no',
        ]);

        // Get max order
        $maxOrder = $survey->questions()->max('order') ?? 0;

        $survey->questions()->create([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'scale_type' => $request->type === 'scale' ? $request->scale_type : null,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.surveys.questions', $survey->id)
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Remove question from database.
     */
    public function destroyQuestion(SurveyQuestion $question)
    {
        $survey = $question->survey;
        $this->authorizeSurvey($survey);

        $question->delete();

        return redirect()->route('admin.surveys.questions', $survey->id)
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * View survey analysis / aggregation results.
     */
    public function results(Survey $survey, Request $request)
    {
        $this->authorizeSurvey($survey);
        
        // Force clear view cache on this specific page so the user ALWAYS sees the newest Blade changes!
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        $teacherType = $request->input('teacher_type'); // 'kejuruan', 'umum', or null

        // Base query for responses count
        $responsesQuery = $survey->responses();
        if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
            $responsesQuery->where('teacher_type', $teacherType);
        }
        $totalResponses = $responsesQuery->count();

        $questions = $survey->questions()->get();
        $results = [];

        foreach ($questions as $question) {
            if ($question->type === 'scale') {
                $scaleType = $question->scale_type ?? 'likert_5';
                
                // Get ratings summary with filter
                $ratingsQuery = DB::table('survey_answers')
                    ->join('survey_responses', 'survey_answers.response_id', '=', 'survey_responses.id')
                    ->where('survey_responses.survey_id', $survey->id)
                    ->where('survey_answers.question_id', $question->id);

                if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
                    $ratingsQuery->where('survey_responses.teacher_type', $teacherType);
                }

                $ratingsSummary = $ratingsQuery->select('survey_answers.rating', DB::raw('count(*) as count'))
                    ->groupBy('survey_answers.rating')
                    ->get()
                    ->pluck('count', 'rating')
                    ->toArray();

                $distribution = [];
                $sum = 0;
                $count = 0;
                
                if ($scaleType === 'yes_no') {
                    $min = 0;
                    $max = 1;
                } elseif ($scaleType === 'likert_4') {
                    $min = 1;
                    $max = 4;
                } else {
                    $min = 1;
                    $max = 5;
                }

                $totalQuestionAnswers = array_sum($ratingsSummary);

                for ($i = $min; $i <= $max; $i++) {
                    $c = $ratingsSummary[$i] ?? 0;
                    $distribution[$i] = [
                        'count' => $c,
                        'percentage' => $totalQuestionAnswers > 0 ? round(($c / $totalQuestionAnswers) * 100, 1) : 0
                    ];
                    $sum += ($i * $c);
                    $count += $c;
                }

                if ($scaleType === 'yes_no') {
                    $yesCount = $ratingsSummary[1] ?? 0;
                    $average = $count > 0 ? round(($yesCount / $count) * 100, 1) : 0;
                } else {
                    $average = $count > 0 ? round($sum / $count, 2) : 0;
                }

                $results[] = [
                    'question' => $question,
                    'type' => 'scale',
                    'scale_type' => $scaleType,
                    'average' => $average,
                    'distribution' => $distribution,
                    'total_answers' => $count,
                    'min' => $min,
                    'max' => $max
                ];
            } else {
                // Open text answers with filter
                $textQuery = DB::table('survey_answers')
                    ->join('survey_responses', 'survey_answers.response_id', '=', 'survey_responses.id')
                    ->join('users', 'survey_responses.user_id', '=', 'users.id')
                    ->where('survey_responses.survey_id', $survey->id)
                    ->where('survey_answers.question_id', $question->id)
                    ->whereNotNull('survey_answers.answer_text')
                    ->where('survey_answers.answer_text', '!=', '');

                if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
                    $textQuery->where('survey_responses.teacher_type', $teacherType);
                }

                $textAnswers = $textQuery->select('survey_answers.id', 'survey_answers.answer_text', 'users.name as respondent_name', 'survey_responses.teacher_type', 'survey_responses.created_at', 'survey_answers.essay_score')
                    ->orderBy('survey_responses.created_at', 'desc')
                    ->get();

                $results[] = [
                    'question' => $question,
                    'type' => 'text',
                    'answers' => $textAnswers
                ];
            }
        }

        // Fetch detailed individual responses with filter
        $individualQuery = SurveyResponse::with(['user', 'answers.question'])
            ->where('survey_id', $survey->id);

        if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
            $individualQuery->where('teacher_type', $teacherType);
        }

        $individualResponses = $individualQuery->orderBy('created_at', 'desc')->get();

        // Calculate average score for each respondent (excluding Yes/No scale type, but including scored essays)
        foreach ($individualResponses as $response) {
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
            $response->average_score = $count > 0 ? round($sum / $count, 2) : '-';
        }

        // Sort by highest average score
        $individualResponses = $individualResponses->sortByDesc(function ($response) {
            return is_numeric($response->average_score) ? (float) $response->average_score : -1;
        })->values();

        // Calculate total target users (population)
        $totalTargetUsers = 0;
        if ($survey->target_respondent === 'guru') {
            $totalTargetUsers = \App\Models\Teacher::where(function($q) use ($survey) {
                if ($survey->school_id) {
                    $q->where('school_id', $survey->school_id);
                }
            })->count();
        } elseif ($survey->target_respondent === 'siswa') {
            if (class_exists(\App\Models\Student::class)) {
                $totalTargetUsers = \App\Models\Student::where(function($q) use ($survey) {
                    if ($survey->school_id) {
                        $q->where('school_id', $survey->school_id);
                    }
                })->count();
            }
        }

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        return view('admin.surveys.results_v2', compact('survey', 'totalResponses', 'results', 'individualResponses', 'teacherType', 'totalTargetUsers'));
    }

    /**
     * Download survey results as PDF.
     */
    public function downloadPdf(Survey $survey, Request $request)
    {
        $this->authorizeSurvey($survey);
        
        $teacherType = $request->input('teacher_type');

        $responsesQuery = $survey->responses();
        if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
            $responsesQuery->where('teacher_type', $teacherType);
        }
        $totalResponses = $responsesQuery->count();

        $questions = $survey->questions()->get();
        $results = [];

        foreach ($questions as $question) {
            if ($question->type === 'scale') {
                $scaleType = $question->scale_type ?? 'likert_5';
                
                $ratingsQuery = DB::table('survey_answers')
                    ->join('survey_responses', 'survey_answers.response_id', '=', 'survey_responses.id')
                    ->where('survey_responses.survey_id', $survey->id)
                    ->where('survey_answers.question_id', $question->id);

                if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
                    $ratingsQuery->where('survey_responses.teacher_type', $teacherType);
                }

                $ratingsSummary = $ratingsQuery->select('survey_answers.rating', DB::raw('count(*) as count'))
                    ->groupBy('survey_answers.rating')
                    ->get()
                    ->pluck('count', 'rating')
                    ->toArray();

                $sum = 0;
                $count = 0;
                
                if ($scaleType === 'yes_no') {
                    $min = 0; $max = 1;
                } elseif ($scaleType === 'likert_4') {
                    $min = 1; $max = 4;
                } else {
                    $min = 1; $max = 5;
                }

                $totalQuestionAnswers = array_sum($ratingsSummary);
                $distribution = [];

                for ($i = $min; $i <= $max; $i++) {
                    $c = $ratingsSummary[$i] ?? 0;
                    $distribution[$i] = [
                        'count' => $c,
                        'percentage' => $totalQuestionAnswers > 0 ? round(($c / $totalQuestionAnswers) * 100, 1) : 0
                    ];
                    $sum += ($i * $c);
                    $count += $c;
                }

                if ($scaleType === 'yes_no') {
                    $yesCount = $ratingsSummary[1] ?? 0;
                    $average = $count > 0 ? round(($yesCount / $count) * 100, 1) : 0;
                } else {
                    $average = $count > 0 ? round($sum / $count, 2) : 0;
                }

                $results[] = [
                    'question' => $question,
                    'type' => 'scale',
                    'scale_type' => $scaleType,
                    'average' => $average,
                    'distribution' => $distribution,
                    'total_answers' => $count
                ];
            } else {
                $textQuery = DB::table('survey_answers')
                    ->join('survey_responses', 'survey_answers.response_id', '=', 'survey_responses.id')
                    ->join('users', 'survey_responses.user_id', '=', 'users.id')
                    ->where('survey_responses.survey_id', $survey->id)
                    ->where('survey_answers.question_id', $question->id)
                    ->whereNotNull('survey_answers.answer_text')
                    ->where('survey_answers.answer_text', '!=', '');

                if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
                    $textQuery->where('survey_responses.teacher_type', $teacherType);
                }

                $textAnswers = $textQuery->select('survey_answers.id', 'survey_answers.answer_text', 'users.name as respondent_name', 'survey_responses.teacher_type', 'survey_responses.created_at', 'survey_answers.essay_score')
                    ->orderBy('survey_responses.created_at', 'desc')
                    ->get();

                $results[] = [
                    'question' => $question,
                    'type' => 'text',
                    'answers' => $textAnswers
                ];
            }
        }

        $individualQuery = SurveyResponse::with(['user', 'answers.question'])
            ->where('survey_id', $survey->id);

        if ($survey->target_respondent === 'guru' && in_array($teacherType, ['kejuruan', 'umum'])) {
            $individualQuery->where('teacher_type', $teacherType);
        }

        $individualResponses = $individualQuery->orderBy('created_at', 'desc')->get();

        foreach ($individualResponses as $response) {
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
            $response->average_score = $count > 0 ? round($sum / $count, 2) : '-';
        }

        $individualResponses = $individualResponses->sortByDesc(function ($response) {
            return is_numeric($response->average_score) ? (float) $response->average_score : -1;
        })->values();

        $totalTargetUsers = 0;
        if ($survey->target_respondent === 'guru') {
            $totalTargetUsers = \App\Models\Teacher::where(function($q) use ($survey) {
                if ($survey->school_id) {
                    $q->where('school_id', $survey->school_id);
                }
            })->count();
        } elseif ($survey->target_respondent === 'siswa') {
            if (class_exists(\App\Models\Student::class)) {
                $totalTargetUsers = \App\Models\Student::where(function($q) use ($survey) {
                    if ($survey->school_id) {
                        $q->where('school_id', $survey->school_id);
                    }
                })->count();
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.surveys.pdf', compact('survey', 'totalResponses', 'results', 'individualResponses', 'teacherType', 'totalTargetUsers'));
        return $pdf->download('Hasil_Survey_' . str_replace(' ', '_', $survey->title) . '.pdf');
    }

    /**
     * Update rating score for essay answer manually.
     */
    public function updateEssayScore(\App\Models\SurveyAnswer $answer, Request $request)
    {
        $request->validate([
            'essay_score' => 'nullable|integer|between:1,5',
        ]);

        $answer->update([
            'essay_score' => $request->input('essay_score')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nilai esai berhasil disimpan.',
            'essay_score' => $answer->essay_score
        ]);
    }

    /**
     * Delete survey response and its answers.
     */
    public function destroyResponse(SurveyResponse $response)
    {
        $this->authorizeSurvey($response->survey);
        $surveyId = $response->survey_id;
        $response->delete(); // Cascading delete answers di database

        return redirect()->route('admin.surveys.results', $surveyId)
            ->with('success', 'Tanggapan survei responden berhasil dihapus/direset.');
    }
}
