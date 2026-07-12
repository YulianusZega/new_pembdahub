<?php

namespace App\Http\Controllers;

use App\Models\AlumniForum;
use App\Models\AlumniForumReply;
use Illuminate\Http\Request;

class AlumniForumController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->alumniDirectory->school_id ?? null;

        if (!$schoolId) {
            abort(403, 'Akses ditolak. Anda belum melengkapi data sekolah.');
        }

        $category = $request->get('category');
        $search = $request->get('search');

        $query = AlumniForum::with(['user', 'replies'])
            ->where('school_id', $schoolId);

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $threads = $query->latest()->paginate(15);
        $categories = AlumniForum::CATEGORIES;

        return view('alumni.forum.index', compact('threads', 'category', 'search', 'categories'));
    }

    public function create()
    {
        $categories = AlumniForum::CATEGORIES;
        return view('alumni.forum.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'category' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();
        $schoolId = $user->alumniDirectory->school_id ?? null;

        if (!$schoolId) {
            abort(403, 'Akses ditolak.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('alumni_forums', 'public');
        }

        AlumniForum::create([
            'user_id' => $user->id,
            'school_id' => $schoolId,
            'category' => $request->category,
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('alumni.forum.index')->with('success', 'Topik berhasil dibuat!');
    }

    public function show(AlumniForum $forum)
    {
        $user = auth()->user();
        $schoolId = $user->alumniDirectory->school_id ?? null;

        if ($forum->school_id !== $schoolId) {
            abort(403, 'Akses ditolak. Ini forum dari unit sekolah lain.');
        }

        $forum->increment('views_count');
        $forum->load(['user', 'replies.user']);

        return view('alumni.forum.show', compact('forum'));
    }

    public function reply(Request $request, AlumniForum $forum)
    {
        $request->validate(['content' => 'required']);

        $user = auth()->user();
        $schoolId = $user->alumniDirectory->school_id ?? null;

        if ($forum->school_id !== $schoolId) {
            abort(403, 'Akses ditolak.');
        }

        AlumniForumReply::create([
            'alumni_forum_id' => $forum->id,
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim!');
    }
}
