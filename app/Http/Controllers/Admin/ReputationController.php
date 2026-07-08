<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reputation;
use App\Models\ReputationLog;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReputationController extends Controller
{
    /**
     * View all reputation logs with filtering
     */
    public function logs(Request $request)
    {
        $query = ReputationLog::with(['user.teacher', 'user.student', 'user.school']);

        // Filter by school
        if ($request->filled('school_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Search by user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($sq) => $sq->where('full_name', 'like', "%{$search}%"))
                  ->orWhereHas('teacher', fn($tq) => $tq->where('full_name', 'like', "%{$search}%"));
            });
        }

        $logs = $query->latest()->paginate(50)->withQueryString();
        $schools = School::orderBy('name')->get();

        return view('admin.reputation.logs', compact('logs', 'schools'));
    }

    /**
     * Form to award points manually
     */
    public function awardForm()
    {
        $schools = School::orderBy('name')->get();
        return view('admin.reputation.award', compact('schools'));
    }

    /**
     * Process manual point awarding
     */
    public function award(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer',
            'category' => 'required|string|max:50',
            'description' => 'required|string|max:255',
        ]);

        ReputationLog::log(
            $validated['user_id'],
            $validated['points'],
            $validated['category'],
            $validated['description'] . ' (Manual Award by Admin: ' . auth()->user()->name . ')'
        );

        return redirect()->route('admin.reputation.logs')
            ->with('success', "Berhasil memberikan {$validated['points']} poin kepada pengguna.");
    }

    /**
     * AJAX: Search users for awarding points
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('q');
        $schoolId = $request->get('school_id');

        $users = User::where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($sq) => $sq->where('full_name', 'like', "%{$search}%"))
                  ->orWhereHas('teacher', fn($tq) => $tq->where('full_name', 'like', "%{$search}%"));
            })
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->with(['student', 'teacher', 'school'])
            ->limit(10)
            ->get()
            ->map(function($user) {
                $name = $user->student ? $user->student->full_name : ($user->teacher ? $user->teacher->full_name : $user->name);
                return [
                    'id' => $user->id,
                    'text' => $name . " ({$user->role}) - " . ($user->school->name ?? 'Internal'),
                ];
            });

        return response()->json($users);
    }

    /**
     * Delete a log and reverse its points
     */
    public function destroy(ReputationLog $log)
    {
        ReputationLog::removeLog($log->user_id, $log->reference_type, $log->reference_id ?? 'manual_' . $log->id);
        
        // If it was a manual log without reference_type
        if ($log->exists) {
            $pointsToSubtract = $log->points;
            $rep = Reputation::where('user_id', $log->user_id)->first();
            if ($rep) {
                $rep->total_points -= $pointsToSubtract;
                $rep->updateLevel();
                $rep->save();
            }
            $log->delete();
        }

        return back()->with('success', 'Log poin berhasil dihapus dan skor telah disesuaikan.');
    }
}
