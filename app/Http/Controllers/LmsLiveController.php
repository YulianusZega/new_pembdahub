<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LmsGame;
use App\Models\LmsLiveSession;
use App\Models\LmsLivePlayer;
use App\Models\LmsLiveAnswer;
use Illuminate\Support\Str;

class LmsLiveController extends Controller
{
    // ==========================================
    // HOST (GURU) METHODS
    // ==========================================

    public function createRoom(Request $request, LmsGame $game)
    {
        // Pastikan hanya game tipe tertentu yang bisa di-host (quiz, true_false)
        if (!in_array($game->game_type, ['quiz', 'true_false'])) {
            return back()->with('error', 'Hanya Kuis atau Benar/Salah yang dapat dimainkan secara live.');
        }

        // Generate a 6-digit random PIN
        $pin = null;
        do {
            $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (LmsLiveSession::where('pin_code', $pin)->exists());

        $session = LmsLiveSession::create([
            'game_id' => $game->id,
            'host_id' => auth()->id(),
            'pin_code' => $pin,
            'status' => 'waiting',
            'current_question_index' => 0
        ]);

        return redirect()->route('guru.lms.live.host', $session->id);
    }

    public function hostUI(LmsLiveSession $session)
    {
        if ($session->host_id !== auth()->id()) abort(403);

        $game = $session->game;
        return view('guru.lms.live.host', compact('session', 'game'));
    }

    public function pollHost(LmsLiveSession $session)
    {
        if ($session->host_id !== auth()->id()) abort(403);

        // Fetch current active players
        $players = $session->players()->orderBy('score', 'desc')->get();
        
        // Count answers for current question
        $answersCount = $session->answers()->where('question_index', $session->current_question_index)->count();

        // If status is leaderboard, return answer distribution (A, B, C, D)
        $distribution = [];
        if ($session->status === 'leaderboard') {
            $answers = $session->answers()->where('question_index', $session->current_question_index)->get();
            foreach($answers as $ans) {
                if(!isset($distribution[$ans->answer_value])) $distribution[$ans->answer_value] = 0;
                $distribution[$ans->answer_value]++;
            }
        }

        return response()->json([
            'status' => $session->status,
            'current_question_index' => $session->current_question_index,
            'players' => $players,
            'answersCount' => $answersCount,
            'distribution' => $distribution
        ]);
    }

    public function updateState(Request $request, LmsLiveSession $session)
    {
        if ($session->host_id !== auth()->id()) abort(403);

        $action = $request->action; // 'start', 'next', 'show_leaderboard', 'end'

        if ($action === 'start') {
            $session->update([
                'status' => 'question',
                'current_question_index' => 0,
                'question_started_at' => now()
            ]);
        } elseif ($action === 'next') {
            $session->update([
                'status' => 'question',
                'current_question_index' => $session->current_question_index + 1,
                'question_started_at' => now()
            ]);
        } elseif ($action === 'show_leaderboard') {
            $session->update([
                'status' => 'leaderboard'
            ]);
        } elseif ($action === 'end') {
            $session->update([
                'status' => 'finished'
            ]);
        }

        return response()->json(['success' => true]);
    }

    // ==========================================
    // PLAYER (SISWA) METHODS
    // ==========================================

    public function playerJoin()
    {
        // Try to auto-fill nickname if student is logged in
        $defaultName = '';
        if (auth()->check() && auth()->user()->role === 'siswa') {
            $defaultName = explode(' ', auth()->user()->name)[0]; // First name
        }
        return view('siswa.lms.live.join', compact('defaultName'));
    }

    public function processJoin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
            'nickname' => 'required|string|max:20'
        ]);

        $session = LmsLiveSession::where('pin_code', $request->pin)->where('status', 'waiting')->first();

        if (!$session) {
            return back()->with('error', 'PIN tidak ditemukan atau permainan sudah dimulai.');
        }

        // Check if nickname exists
        if ($session->players()->where('nickname', $request->nickname)->exists()) {
            return back()->with('error', 'Nama panggilan sudah dipakai di ruangan ini.');
        }

        $player = LmsLivePlayer::create([
            'session_id' => $session->id,
            'student_id' => auth()->check() && auth()->user()->role === 'siswa' ? auth()->user()->student->id : null,
            'nickname' => $request->nickname,
        ]);

        // Save player ID in session to recognize them
        session()->put('live_player_id', $player->id);

        return redirect()->route('live.play', $session->id);
    }

    public function playerUI(LmsLiveSession $session)
    {
        $playerId = session('live_player_id');
        if (!$playerId) return redirect()->route('live.join');

        $player = LmsLivePlayer::find($playerId);
        if (!$player || $player->session_id !== $session->id) {
            return redirect()->route('live.join');
        }

        return view('siswa.lms.live.play', compact('session', 'player'));
    }

    public function pollPlayer(LmsLiveSession $session)
    {
        $playerId = session('live_player_id');
        $player = LmsLivePlayer::find($playerId);

        // Has player answered current question?
        $hasAnswered = false;
        $myAnswer = null;
        if ($session->status === 'question' || $session->status === 'leaderboard') {
            $ans = LmsLiveAnswer::where('session_id', $session->id)
                ->where('player_id', $playerId)
                ->where('question_index', $session->current_question_index)
                ->first();
            if ($ans) {
                $hasAnswered = true;
                $myAnswer = [
                    'is_correct' => $ans->is_correct,
                    'points' => $ans->points_earned
                ];
            }
        }

        return response()->json([
            'status' => $session->status,
            'current_question_index' => $session->current_question_index,
            'has_answered' => $hasAnswered,
            'my_answer' => $myAnswer,
            'player_score' => $player ? $player->score : 0,
            'rank' => $player ? LmsLivePlayer::where('session_id', $session->id)->where('score', '>', $player->score)->count() + 1 : 0
        ]);
    }

    public function submitAnswer(Request $request, LmsLiveSession $session)
    {
        $playerId = session('live_player_id');
        $player = LmsLivePlayer::find($playerId);

        if (!$player || $session->status !== 'question') {
            return response()->json(['error' => 'Invalid state'], 400);
        }

        // Prevent double answer
        if (LmsLiveAnswer::where('session_id', $session->id)
            ->where('player_id', $playerId)
            ->where('question_index', $session->current_question_index)
            ->exists()) {
            return response()->json(['error' => 'Already answered'], 400);
        }

        $gameData = $session->game->game_data;
        $questions = $gameData['questions'] ?? [];
        if (!isset($questions[$session->current_question_index])) {
            return response()->json(['error' => 'Question not found'], 400);
        }

        $q = $questions[$session->current_question_index];
        $isCorrect = false;

        if ($session->game->game_type === 'quiz') {
            $isCorrect = (int)$request->answer === (int)$q['correctAnswer'];
        } elseif ($session->game->game_type === 'true_false') {
            // answer is true or false
            $isCorrect = filter_var($request->answer, FILTER_VALIDATE_BOOLEAN) === filter_var($q['isTrue'], FILTER_VALIDATE_BOOLEAN);
        }

        // Calculate points based on time (Kahoot style)
        // Max 1000 points. The faster, the higher.
        $points = 0;
        $timeTakenMs = 0;
        if ($isCorrect) {
            $timeTaken = now()->diffInMilliseconds($session->question_started_at);
            $timeTakenMs = $timeTaken;
            $maxTime = ($session->game->time_limit ?? 20) * 1000; // default 20s
            if ($timeTaken > $maxTime) $timeTaken = $maxTime;
            
            // Equation: 1000 * (1 - (time_taken / max_time) / 2)
            $points = round(1000 * (1 - ($timeTaken / $maxTime) / 2));
            if ($points < 500) $points = 500; // minimum 500 points for correct answer
        }

        LmsLiveAnswer::create([
            'session_id' => $session->id,
            'player_id' => $player->id,
            'question_index' => $session->current_question_index,
            'answer_value' => $request->answer,
            'is_correct' => $isCorrect,
            'points_earned' => $points,
            'time_taken_ms' => $timeTakenMs
        ]);

        if ($isCorrect) {
            $player->increment('score', $points);
            $player->increment('streak');
        } else {
            $player->update(['streak' => 0]);
        }

        return response()->json(['success' => true]);
    }
}
