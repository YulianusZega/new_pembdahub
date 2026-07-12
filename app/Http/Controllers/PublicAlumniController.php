<?php

namespace App\Http\Controllers;

use App\Models\AlumniDirectory;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicAlumniController extends Controller
{
    /**
     * Show the public registration form.
     */
    public function registerForm()
    {
        $schools = School::orderBy('name')->get();
        // Array of years from 1970 to current year
        $years = range(now()->year, 1970);
        
        return view('landing.alumni_register', compact('schools', 'years'));
    }

    /**
     * Handle the registration submission.
     */
    public function registerSubmit(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'graduation_year' => 'required|integer|min:1970|max:' . now()->year,
            'last_class' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // max 2MB
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs('alumni_photos', $filename, 'public');
        }

        AlumniDirectory::create([
            'full_name' => $validated['full_name'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'school_id' => $validated['school_id'],
            'graduation_year' => $validated['graduation_year'],
            'last_class' => $validated['last_class'] ?? null,
            'message' => $validated['message'] ?? null,
            'photo_path' => $photoPath,
            'is_approved' => false, // Requires admin approval to show publicly if needed
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Data pendaftaran Anda berhasil dikirim dan akan diverifikasi oleh Admin. Selamat bergabung kembali di Ikatan Alumni Yayasan Perguruan PEMBDA Nias!');
    }
}
