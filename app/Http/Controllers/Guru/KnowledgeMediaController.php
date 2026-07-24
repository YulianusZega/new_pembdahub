<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeMaterial;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KnowledgeMediaController extends Controller
{
    /**
     * Dashboard / Index Guru Knowledge & Media
     */
    public function index()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return redirect()->route('guru.dashboard')->with('error', 'Profil Guru tidak ditemukan.');
        }

        $materials = KnowledgeMaterial::with('subject')
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->paginate(12);

        // Calculate Guru Stats & Points
        $allMaterials = KnowledgeMaterial::where('teacher_id', $teacher->id)->get();
        $totalUploads = $allMaterials->count();
        $totalLikes = $allMaterials->sum('likes_count');
        $totalBookmarks = $allMaterials->sum('bookmarks_count');
        $totalViews = $allMaterials->sum('views_count');
        
        $totalPoints = $allMaterials->reduce(function ($carry, $item) {
            return $carry + $item->points;
        }, 0);

        return view('guru.knowledge.index', compact(
            'materials',
            'teacher',
            'totalUploads',
            'totalLikes',
            'totalBookmarks',
            'totalViews',
            'totalPoints'
        ));
    }

    /**
     * Show form to create new knowledge material
     */
    public function create()
    {
        $teacher = Auth::user()->teacher;
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();

        return view('guru.knowledge.create', compact('teacher', 'subjects'));
    }

    /**
     * Store new material
     */
    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return back()->with('error', 'Profil Guru tidak ditemukan.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,video,audio,link',
            'category_type' => 'required|in:sekolah,umum',
            'subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480', // Max 20MB
            'external_url' => 'nullable|url',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240', // Max 10MB (10240 KB)
            'is_public' => 'nullable|boolean',
            'allow_download' => 'nullable|boolean',
        ], [
            'thumbnail.max' => 'Ukuran file thumbnail tidak boleh lebih dari 10 MB (10.240 Kilobyte).',
            'thumbnail.image' => 'File thumbnail harus berupa file gambar.',
            'thumbnail.mimes' => 'Format gambar yang didukung hanya JPG, JPEG, PNG, dan WEBP.',
            'file.max' => 'Ukuran file media tidak boleh lebih dari 20 MB (20.480 Kilobyte).',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('knowledge/files', 'public');
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('knowledge/thumbnails', 'public');
        }

        $slug = Str::slug($request->title) . '-' . Str::random(6);

        KnowledgeMaterial::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->category_type === 'sekolah' ? $request->subject_id : null,
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'type' => $request->type,
            'category_type' => $request->category_type,
            'file_path' => $filePath,
            'external_url' => $request->external_url,
            'thumbnail_path' => $thumbnailPath,
            'is_public' => $request->has('is_public') ? (bool) $request->is_public : true,
            'allow_download' => $request->has('allow_download') ? (bool) $request->allow_download : true,
        ]);

        return redirect()->route('guru.knowledge.index')->with('success', 'Materi Pembda Knowledge & Media berhasil dipublikasikan!');
    }

    /**
     * Form Edit Material
     */
    public function edit(KnowledgeMaterial $knowledge)
    {
        $teacher = Auth::user()->teacher;

        if ($knowledge->teacher_id !== $teacher->id) {
            abort(403, 'Akses ditolak.');
        }

        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();

        return view('guru.knowledge.edit', compact('knowledge', 'subjects'));
    }

    /**
     * Update Material
     */
    public function update(Request $request, KnowledgeMaterial $knowledge)
    {
        $teacher = Auth::user()->teacher;

        if ($knowledge->teacher_id !== $teacher->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,video,audio,link',
            'category_type' => 'required|in:sekolah,umum',
            'subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
            'external_url' => 'nullable|url',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240', // Max 10MB (10240 KB)
            'is_public' => 'nullable|boolean',
            'allow_download' => 'nullable|boolean',
        ], [
            'thumbnail.max' => 'Ukuran file thumbnail tidak boleh lebih dari 10 MB (10.240 Kilobyte).',
            'thumbnail.image' => 'File thumbnail harus berupa file gambar.',
            'thumbnail.mimes' => 'Format gambar yang didukung hanya JPG, JPEG, PNG, dan WEBP.',
            'file.max' => 'Ukuran file media tidak boleh lebih dari 20 MB (20.480 Kilobyte).',
        ]);

        $filePath = $knowledge->file_path;
        if ($request->hasFile('file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('knowledge/files', 'public');
        }

        $thumbnailPath = $knowledge->thumbnail_path;
        if ($request->hasFile('thumbnail')) {
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $request->file('thumbnail')->store('knowledge/thumbnails', 'public');
        }

        $knowledge->update([
            'subject_id' => $request->category_type === 'sekolah' ? $request->subject_id : null,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'category_type' => $request->category_type,
            'file_path' => $filePath,
            'external_url' => $request->external_url,
            'thumbnail_path' => $thumbnailPath,
            'is_public' => $request->has('is_public') ? (bool) $request->is_public : false,
            'allow_download' => $request->has('allow_download') ? (bool) $request->allow_download : false,
        ]);

        return redirect()->route('guru.knowledge.index')->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Delete Material
     */
    public function destroy(KnowledgeMaterial $knowledge)
    {
        $teacher = Auth::user()->teacher;

        if ($knowledge->teacher_id !== $teacher->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($knowledge->file_path && Storage::disk('public')->exists($knowledge->file_path)) {
            Storage::disk('public')->delete($knowledge->file_path);
        }

        if ($knowledge->thumbnail_path && Storage::disk('public')->exists($knowledge->thumbnail_path)) {
            Storage::disk('public')->delete($knowledge->thumbnail_path);
        }

        $knowledge->delete();

        return redirect()->route('guru.knowledge.index')->with('success', 'Materi berhasil dihapus.');
    }
}
