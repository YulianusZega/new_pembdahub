<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\AdmissionFee;
use App\Models\AdmissionTest;
use App\Models\RegistrationWave;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class PsbSettingController extends Controller
{
    /**
     * Display selection of schools to manage PSB settings.
     */
    public function index()
    {
        $schools = School::schoolsOnly()
            ->withCount(['registrationWaves'])
            ->get();
            
        return view('admin.psb.settings.index', compact('schools'));
    }

    /**
     * Show settings for a specific school.
     */
    public function edit(School $school)
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $currentYear = AcademicYear::where('is_active', true)->first();
        
        // Load configurations
        $fees = AdmissionFee::where('school_id', $school->id)->get();
        $tests = AdmissionTest::where('school_id', $school->id)->get();
        $waves = RegistrationWave::where('school_id', $school->id)->get();

        return view('admin.psb.settings.edit', compact(
            'school', 
            'academicYears', 
            'currentYear', 
            'fees', 
            'tests', 
            'waves'
        ));
    }

    /**
     * Update school-specific PSB settings.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'psb_contact_person' => 'nullable|string|max:100',
            'psb_contact_phone' => 'nullable|string|max:20',
            'psb_opening_hours' => 'nullable|string',
            'psb_secretariat' => 'nullable|string',
            'psb_description' => 'nullable|string',
            'psb_is_active' => 'sometimes|boolean',
            'psb_required_documents' => 'nullable|array',
            'requires_test' => 'sometimes|boolean',
            'test_type' => 'nullable|string',
        ]);

        $validated['psb_is_active'] = $request->has('psb_is_active');
        $validated['requires_test'] = $request->has('requires_test');
        $validated['psb_required_documents'] = $request->input('psb_required_documents', []);

        $school->update($validated);

        return redirect()->back()->with('success', 'Pengaturan PSB Unit ' . $school->name . ' berhasil diperbarui.');
    }

    /**
     * Add a custom document type for the school.
     */
    public function addCustomDocument(Request $request, School $school)
    {
        $request->validate([
            'custom_doc_label' => 'required|string|max:100',
        ]);

        $label = trim($request->input('custom_doc_label'));
        // Generate a safe key from the label
        $key = 'custom_' . preg_replace('/[^a-z0-9]+/', '_', strtolower($label));
        // Ensure unique key by appending timestamp if needed
        $key = $key . '_' . time();

        $customDocs = $school->psb_custom_document_types ?? [];
        $customDocs[] = ['key' => $key, 'label' => $label];

        $school->update(['psb_custom_document_types' => $customDocs]);

        return redirect()->back()->with('success', 'Dokumen "' . $label . '" berhasil ditambahkan.');
    }

    /**
     * Remove a custom document type from the school.
     */
    public function removeCustomDocument(Request $request, School $school)
    {
        $keyToRemove = $request->input('document_key');
        $customDocs = $school->psb_custom_document_types ?? [];

        $customDocs = array_values(array_filter($customDocs, function ($doc) use ($keyToRemove) {
            return ($doc['key'] ?? '') !== $keyToRemove;
        }));

        // Also remove from required documents if it was selected
        $requiredDocs = $school->psb_required_documents ?? [];
        $requiredDocs = array_values(array_filter($requiredDocs, function ($key) use ($keyToRemove) {
            return $key !== $keyToRemove;
        }));

        $school->update([
            'psb_custom_document_types' => $customDocs,
            'psb_required_documents' => $requiredDocs,
        ]);

        return redirect()->back()->with('success', 'Dokumen kustom berhasil dihapus.');
    }

    /**
     * Store a new admission fee for the school.
     */
    public function feeStore(Request $request, School $school)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'fee_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $validated['school_id'] = $school->id;
        $validated['is_active'] = true;

        AdmissionFee::create($validated);

        return redirect()->back()->with('success', 'Komponen biaya berhasil ditambahkan.');
    }

    /**
     * Remove an admission fee.
     */
    public function feeDestroy(AdmissionFee $fee)
    {
        $fee->delete();
        return redirect()->back()->with('success', 'Komponen biaya berhasil dihapus.');
    }

    /**
     * Store a new registration wave for the school.
     */
    public function waveStore(Request $request, School $school)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:100',
            'wave_number' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quota' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['school_id'] = $school->id;
        $validated['is_active'] = true;
        $validated['registered_count'] = 0;

        RegistrationWave::create($validated);

        return redirect()->back()->with('success', 'Gelombang pendaftaran berhasil ditambahkan.');
    }

    /**
     * Remove a registration wave.
     */
    public function waveDestroy(RegistrationWave $wave)
    {
        $wave->delete();
        return redirect()->back()->with('success', 'Gelombang pendaftaran berhasil dihapus.');
    }

    /**
     * Toggle wave active status.
     */
    public function waveToggle(RegistrationWave $wave)
    {
        $wave->is_active = !$wave->is_active;
        $wave->save();
        
        return redirect()->back()->with('success', 'Status gelombang berhasil diubah.');
    }
}
