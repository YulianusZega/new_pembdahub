<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard alumni.
     */
    public function index()
    {
        $user = Auth::user();
        $alumni = $user->alumniDirectory;

        if (!$alumni) {
            return redirect()->route('landing.alumni-register')
                ->with('error', 'Profil direktori alumni Anda tidak ditemukan.');
        }

        // Cek status Tracer Study (ambil AlumniProfile yang terhubung dengan email user)
        $alumniProfile = \App\Models\AlumniProfile::where('email', $user->email)->first();
        $hasFilledTracer = false;
        if ($alumniProfile) {
            $hasFilledTracer = \App\Models\TracerStudy::where('alumni_profile_id', $alumniProfile->id)->exists();
        }

        // Ambil lowongan kerja terbaru (maksimal 3)
        $latestJobs = \App\Models\JobPosting::where('is_active', true)->latest()->take(3)->get();

        // Ambil obrolan terbaru di Pembda Space (maksimal 3)
        $latestThreads = \App\Models\ForumThread::with(['user'])
                            ->latest()
                            ->take(3)
                            ->get();

        return view('alumni.dashboard', compact('user', 'alumni', 'hasFilledTracer', 'latestJobs', 'latestThreads'));
    }
}
