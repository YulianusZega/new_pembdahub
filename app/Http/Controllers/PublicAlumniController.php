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
        // Exclude yayasan, get all unit schools including historical ones
        $schools = School::where('type', '!=', 'yayasan')->orderBy('name')->get();
        // Array of years from 1970 to current year
        $years = range(now()->year, 1970);
        
        // Fetch alumni for gallery preview (no approval needed)
        $approvedAlumni = AlumniDirectory::with('school')
                            ->latest()
                            ->take(12)
                            ->get();
        
        return view('landing.alumni_register', compact('schools', 'years', 'approvedAlumni'));
    }

    /**
     * Handle the registration submission.
     */
    public function registerSubmit(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'alias_name' => 'nullable|string|max:255',
            'gender' => 'required|in:L,P',
            'marital_status' => 'nullable|string|max:50',
            'children_count' => 'nullable|integer|min:0',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
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
            'alias_name' => $validated['alias_name'] ?? null,
            'gender' => $validated['gender'],
            'marital_status' => $validated['marital_status'] ?? null,
            'children_count' => $validated['children_count'] ?? null,
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'school_id' => $validated['school_id'],
            'graduation_year' => $validated['graduation_year'],
            'last_class' => $validated['last_class'] ?? null,
            'message' => $validated['message'] ?? null,
            'photo_path' => $photoPath,
            'is_approved' => true, // Auto approved as requested
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Data Anda telah berhasil dikirim dan ditambahkan ke dalam database Ikatan Alumni Yayasan Perguruan PEMBDA Nias.');
    }
}
