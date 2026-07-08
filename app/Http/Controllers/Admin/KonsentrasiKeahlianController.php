<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Http\Request;

class KonsentrasiKeahlianController extends Controller
{
    public function index(Request $request)
    {
        $query = KonsentrasiKeahlian::with('programKeahlian.school');

        if ($request->filled('program_keahlian_id')) {
            $query->where('program_keahlian_id', $request->program_keahlian_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $konsentrasiKeahlians = $query->orderBy('nama')->get();
        $programKeahlians = ProgramKeahlian::orderBy('nama')->get();

        return view('admin.konsentrasi-keahlians.index', compact('konsentrasiKeahlians', 'programKeahlians'));
    }

    public function show(KonsentrasiKeahlian $konsentrasiKeahlian)
    {
        $konsentrasiKeahlian->load('programKeahlian.school');
        return view('admin.konsentrasi-keahlians.show', compact('konsentrasiKeahlian'));
    }

    public function create(Request $request)
    {
        $programKeahlians = ProgramKeahlian::orderBy('nama')->get();
        $selectedProgramId = $request->get('program_keahlian_id');
        return view('admin.konsentrasi-keahlians.create', compact('programKeahlians', 'selectedProgramId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_keahlian_id' => 'required|exists:program_keahlians,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
        KonsentrasiKeahlian::create($data);
        return redirect()->route('admin.majors.index')->with('success', 'Konsentrasi Keahlian berhasil ditambahkan.');
    }

    public function edit(KonsentrasiKeahlian $konsentrasiKeahlian)
    {
        $programKeahlians = ProgramKeahlian::orderBy('nama')->get();
        return view('admin.konsentrasi-keahlians.edit', compact('konsentrasiKeahlian', 'programKeahlians'));
    }

    public function update(Request $request, KonsentrasiKeahlian $konsentrasiKeahlian)
    {
        $data = $request->validate([
            'program_keahlian_id' => 'required|exists:program_keahlians,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
        $konsentrasiKeahlian->update($data);
        return redirect()->route('admin.majors.index')->with('success', 'Konsentrasi Keahlian berhasil diperbarui.');
    }

    public function destroy(KonsentrasiKeahlian $konsentrasiKeahlian)
    {
        $konsentrasiKeahlian->delete();
        return redirect()->route('admin.majors.index')->with('success', 'Konsentrasi Keahlian berhasil dihapus.');
    }
}
