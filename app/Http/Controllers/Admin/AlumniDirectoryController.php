<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlumniDirectory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniDirectoryController extends Controller
{
    /**
     * Display a listing of the alumni directory.
     */
    public function index(Request $request)
    {
        $query = AlumniDirectory::with('school')->latest();

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }
        
        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        $directories = $query->paginate(20);

        return view('admin.pkl_alumni.directory.index', compact('directories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AlumniDirectory $directory)
    {
        return view('admin.pkl_alumni.directory.show', compact('directory'));
    }

    /**
     * Approve the registration to be shown publicly if needed.
     */
    public function toggleApproval(AlumniDirectory $directory)
    {
        $directory->is_approved = !$directory->is_approved;
        $directory->save();

        return back()->with('success', 'Status persetujuan alumni berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlumniDirectory $directory)
    {
        if ($directory->photo_path) {
            Storage::disk('public')->delete($directory->photo_path);
        }
        
        $directory->delete();

        return redirect()->route('admin.alumni-directory.index')->with('success', 'Data alumni berhasil dihapus.');
    }
}
