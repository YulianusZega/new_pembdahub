<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramKeahlian;
use App\Models\School;
use Illuminate\Http\Request;

class ProgramKeahlianController extends Controller
{
    public function index()
    {
        $programKeahlians = ProgramKeahlian::with('school', 'konsentrasiKeahlians')->orderBy('nama')->get();
        $schools = School::orderBy('name')->get();
        return view('admin.program-keahlians.index', compact('programKeahlians', 'schools'));
    }

    public function create()
    {
        $schools = School::orderBy('name')->get();
        return view('admin.program-keahlians.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
        ProgramKeahlian::create($data);
        return redirect()->route('admin.majors.index')->with('success', 'Program Keahlian berhasil ditambahkan.');
    }

    public function edit(ProgramKeahlian $programKeahlian)
    {
        $schools = School::orderBy('name')->get();
        return view('admin.program-keahlians.edit', compact('programKeahlian', 'schools'));
    }

    public function update(Request $request, ProgramKeahlian $programKeahlian)
    {
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
        $programKeahlian->update($data);
        return redirect()->route('admin.majors.index')->with('success', 'Program Keahlian berhasil diperbarui.');
    }

    public function destroy(ProgramKeahlian $programKeahlian)
    {
        $programKeahlian->delete();
        return redirect()->route('admin.majors.index')->with('success', 'Program Keahlian berhasil dihapus.');
    }
}
