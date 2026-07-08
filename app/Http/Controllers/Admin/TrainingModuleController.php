<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrainingModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingModule::with('author')
            ->orderBy('sort_order', 'asc')
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($targetRole = $request->get('target_role')) {
            $query->whereJsonContains('target_roles', $targetRole);
        }

        if ($request->get('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->get('status') === 'draft') {
            $query->where('is_published', false);
        }

        $modules = $query->paginate(10)->withQueryString();

        return view('admin.training-modules.index', compact('modules'));
    }

    public function create()
    {
        return view('admin.training-modules.form', ['module' => new TrainingModule()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'content'        => 'nullable|string',
            'pdf_file'       => 'nullable|file|mimes:pdf|max:10240',
            'category'       => 'required|in:panduan_umum,fitur_admin,fitur_guru,fitur_siswa,fitur_orangtua,fitur_keuangan,fitur_yayasan',
            'target_roles'   => 'required|array|min:1',
            'target_roles.*' => 'in:superadmin,admin_sekolah,guru,siswa,orang_tua,bendahara,ketua_yayasan',
            'thumbnail_image'=> 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'reading_time'   => 'nullable|integer|min:1',
            'difficulty'     => 'nullable|in:Pemula,Menengah,Mahir',
            'sort_order'     => 'nullable|integer|min:0',
            'is_published'   => 'boolean',
        ]);

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')->store('training-modules', 'public');
        }

        if ($request->hasFile('thumbnail_image')) {
            $validated['thumbnail_image'] = $request->file('thumbnail_image')->store('training-thumbnails', 'public');
        }

        // Generate unique slug
        $slug = Str::slug($validated['title']);
        $count = TrainingModule::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $validated['slug'] = $slug;
        $validated['created_by'] = auth()->id();
        $validated['is_published'] = $request->boolean('is_published');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        TrainingModule::create($validated);

        return redirect()->route('admin.training-modules.index')
            ->with('success', 'Modul pelatihan berhasil ditambahkan!');
    }

    public function show(TrainingModule $trainingModule)
    {
        return view('admin.training-modules.show', ['module' => $trainingModule]);
    }

    public function edit(TrainingModule $trainingModule)
    {
        return view('admin.training-modules.form', ['module' => $trainingModule]);
    }

    public function update(Request $request, TrainingModule $trainingModule)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'content'        => 'nullable|string',
            'pdf_file'       => 'nullable|file|mimes:pdf|max:10240',
            'category'       => 'required|in:panduan_umum,fitur_admin,fitur_guru,fitur_siswa,fitur_orangtua,fitur_keuangan,fitur_yayasan',
            'target_roles'   => 'required|array|min:1',
            'target_roles.*' => 'in:superadmin,admin_sekolah,guru,siswa,orang_tua,bendahara,ketua_yayasan',
            'thumbnail_image'=> 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'reading_time'   => 'nullable|integer|min:1',
            'difficulty'     => 'nullable|in:Pemula,Menengah,Mahir',
            'sort_order'     => 'nullable|integer|min:0',
            'is_published'   => 'boolean',
        ]);

        if ($request->hasFile('pdf_file')) {
            // Delete old PDF
            if ($trainingModule->pdf_file) {
                Storage::disk('public')->delete($trainingModule->pdf_file);
            }
            $validated['pdf_file'] = $request->file('pdf_file')->store('training-modules', 'public');
        }

        if ($request->hasFile('thumbnail_image')) {
            // Delete old thumbnail
            if ($trainingModule->thumbnail_image) {
                Storage::disk('public')->delete($trainingModule->thumbnail_image);
            }
            $validated['thumbnail_image'] = $request->file('thumbnail_image')->store('training-thumbnails', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $trainingModule->update($validated);

        return redirect()->route('admin.training-modules.index')
            ->with('success', 'Modul pelatihan berhasil diperbarui!');
    }

    public function destroy(TrainingModule $trainingModule)
    {
        if ($trainingModule->pdf_file) {
            Storage::disk('public')->delete($trainingModule->pdf_file);
        }
        
        if ($trainingModule->thumbnail_image) {
            Storage::disk('public')->delete($trainingModule->thumbnail_image);
        }

        $trainingModule->delete();

        return redirect()->route('admin.training-modules.index')
            ->with('success', 'Modul pelatihan berhasil dihapus!');
    }

    public function togglePublish(TrainingModule $trainingModule)
    {
        $trainingModule->update([
            'is_published' => !$trainingModule->is_published,
        ]);

        $status = $trainingModule->is_published ? 'dipublikasikan' : 'dijadikan draft';
        return back()->with('success', "Modul pelatihan berhasil {$status}!");
    }

    public function download(TrainingModule $trainingModule)
    {
        if (!$trainingModule->pdf_file || !Storage::disk('public')->exists($trainingModule->pdf_file)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        return Storage::disk('public')->download($trainingModule->pdf_file, $trainingModule->title . '.pdf');
    }
}
