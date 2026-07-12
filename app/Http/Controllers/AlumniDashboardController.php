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

        return view('alumni.dashboard', compact('user', 'alumni'));
    }
}
