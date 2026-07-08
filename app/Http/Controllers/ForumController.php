<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\ForumLike;
use App\Models\ForumMember;
use App\Models\ReputationLog;
use App\Models\CbtExamResult;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ForumController extends Controller
{
    /**
     * Index / Board View
     */
    public function index(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');
        $user = Auth::user();

        $query = ForumThread::with(['user', 'replies', 'likes', 'members'])
            ->pinnedFirst();

        // Scope by category
        if ($category && array_key_exists($category, ForumThread::CATEGORIES)) {
            $query->where('category', $category);
        }

        // Scope by search
        if ($search) {
            $query->search($search);
        }

        $threads = $query->paginate(10)->withQueryString();

        // Get category count for tabs
        $counts = ForumThread::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // Fetch top 5 students for leaderboard
        $topStudents = \App\Models\Reputation::with(['user.student.classroom', 'user.badges'])
            ->whereHas('user', function($q) {
                $q->where('role', 'siswa');
            })
            ->orderBy('total_points', 'desc')
            ->take(5)
            ->get();

        // Fetch active collaborations seeking members
        $activeCollabs = ForumThread::whereIn('category', ['project_idea', 'committee'])
            ->where('status', 'seeking_members')
            ->with(['user', 'likes', 'replies'])
            ->latest()
            ->take(3)
            ->get();

        return view('forum.index', compact('threads', 'counts', 'category', 'search', 'topStudents', 'activeCollabs'));
    }

    /**
     * Create Thread View
     */
    public function create()
    {
        $user = Auth::user();
        $badges = $user->badges()->get();
        
        $cbtResults = collect([]);
        if ($user->isSiswa() && $user->student) {
            $cbtResults = CbtExamResult::where('student_id', $user->student->id)
                ->with('exam')
                ->latest()
                ->take(10)
                ->get();
        }

        return view('forum.create', compact('badges', 'cbtResults'));
    }

    /**
     * Store New Thread
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Info/Announcement category is restricted to teachers/admins
        $allowedCategories = array_keys(ForumThread::CATEGORIES);
        if (!$user->isSuperAdmin() && !$user->isAdminSekolah() && !$user->isGuru()) {
            $allowedCategories = array_diff($allowedCategories, ['info']);
        }

        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|in:' . implode(',', $allowedCategories),
            'image' => 'nullable|image|max:5120', // 5MB limit
            'attachment' => 'nullable|file|max:10240', // 10MB limit
            
            // Collaboration & Charity Specifics
            'charity_target_amount' => 'nullable|numeric|min:0',
            'charity_target_volunteers' => 'nullable|integer|min:0',
            'recruitment_enabled' => 'nullable|boolean',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $threadData = [
                'user_id' => $user->id,
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'status' => ($validated['category'] === 'project_idea' || $validated['category'] === 'committee' || $validated['category'] === 'charity')
                    ? 'seeking_members' 
                    : 'seeking_members',
            ];

            // Handle uploads
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('forum', 'public');
                $threadData['image_path'] = $path;
            }

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('forum_attachments', 'public');
                $threadData['attachment_path'] = $path;
                $threadData['attachment_name'] = $file->getClientOriginalName();
            }

            // Reference linking (Badges / Grades for visual & portfolio categories)
            $perfCategories = ['performance', 'art_gallery', 'talent', 'portfolio'];
            if (in_array($validated['category'], $perfCategories) && !empty($validated['reference_type']) && !empty($validated['reference_id'])) {
                if ($validated['reference_type'] === 'badge') {
                    $threadData['reference_type'] = Badge::class;
                    $threadData['reference_id'] = $validated['reference_id'];
                } elseif ($validated['reference_type'] === 'grade') {
                    $threadData['reference_type'] = CbtExamResult::class;
                    $threadData['reference_id'] = $validated['reference_id'];
                }
            }

            // Charity data
            if ($validated['category'] === 'charity') {
                $threadData['charity_target_amount'] = $validated['charity_target_amount'] ?? null;
                $threadData['charity_target_volunteers'] = $validated['charity_target_volunteers'] ?? null;
                $threadData['charity_current_amount'] = 0;
            }

            $thread = ForumThread::create($threadData);

            // Gamification hook: +15 points for creating thread
            ReputationLog::log($user->id, 15, 'forum', "Membuat thread forum: {$thread->title}", $thread);

            DB::commit();

            return redirect()->route('forum.show', $thread)
                ->with('success', 'Topik diskusi berhasil diterbitkan! (+15 Poin Reputasi)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memposting: ' . $e->getMessage());
        }
    }

    /**
     * Show Thread Details
     */
    public function show(ForumThread $thread)
    {
        $thread->increment('views_count');
        $thread->load(['user', 'replies.user', 'likes', 'members.user']);

        // Build the performance reference view representation if linked
        $perfCard = null;
        $perfCategories = ['performance', 'art_gallery', 'talent', 'portfolio'];
        if (in_array($thread->category, $perfCategories) && $thread->reference_type) {
            if ($thread->reference_type === Badge::class) {
                $perfCard = Badge::find($thread->reference_id);
            } elseif ($thread->reference_type === CbtExamResult::class) {
                $perfCard = CbtExamResult::with('exam')->find($thread->reference_id);
            }
        }

        return view('forum.show', compact('thread', 'perfCard'));
    }

    /**
     * Reply to a Thread
     */
    public function reply(Request $request, ForumThread $thread)
    {
        $user = Auth::user();
        if ($thread->is_locked) {
            return back()->with('error', 'Topik diskusi ini dikunci.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        DB::beginTransaction();
        try {
            $reply = ForumReply::create([
                'forum_thread_id' => $thread->id,
                'user_id' => $user->id,
                'content' => $validated['content'],
            ]);

            // Gamification hook: +5 points for replying
            ReputationLog::log($user->id, 5, 'forum', "Mengomentari diskusi: {$thread->title}", $reply);

            DB::commit();

            return back()->with('success', 'Komentar ditambahkan! (+5 Poin Reputasi)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memposting komentar: ' . $e->getMessage());
        }
    }

    /**
     * Like / Upvote Thread
     */
    public function like(Request $request, ForumThread $thread)
    {
        $user = Auth::user();
        $author = $thread->user;

        $existingLike = ForumLike::where('user_id', $user->id)
            ->where('forum_thread_id', $thread->id)
            ->first();

        DB::beginTransaction();
        try {
            if ($existingLike) {
                // Reverse points
                ReputationLog::removeLog($user->id, ForumLike::class, $existingLike->id);
                if ($author->id !== $user->id) {
                    ReputationLog::removeLog($author->id, ForumThread::class, $thread->id);
                }

                $existingLike->delete();
                $isLiked = false;
                $message = 'Upvote dibatalkan.';
            } else {
                $like = ForumLike::create([
                    'user_id' => $user->id,
                    'forum_thread_id' => $thread->id,
                ]);

                // Liker gets +2 points
                ReputationLog::log($user->id, 2, 'forum_like', "Menyukai topik: {$thread->title}", $like);

                // Author gets +10 points (if not liking own thread)
                if ($author->id !== $user->id) {
                    ReputationLog::log($author->id, 10, 'forum_upvote', "Mendapat upvote pada topik: {$thread->title}", $thread);
                }

                $isLiked = true;
                $message = 'Topik berhasil diupvote! (+2 Poin Liker / +10 Poin Penulis)';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'liked' => $isLiked,
                'likes_count' => $thread->likes()->count(),
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Accept Best Reply / Answer
     */
    public function acceptReply(ForumReply $reply)
    {
        $user = Auth::user();
        $thread = $reply->thread;

        // Verify authority: only thread author or Admin/Teacher can accept
        if ($user->id !== $thread->user_id && !$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Unauthorized');
        }

        DB::beginTransaction();
        try {
            // Unaccept any previous accepted reply for this thread
            ForumReply::where('forum_thread_id', $thread->id)
                ->where('is_accepted', true)
                ->update(['is_accepted' => false]);

            $reply->update(['is_accepted' => true]);

            // Recipient gets +15 points for best answer
            ReputationLog::log($reply->user_id, 15, 'forum_best_answer', "Komentar terpilih sebagai Jawaban Terbaik di topik: {$thread->title}", $reply);

            DB::commit();

            return back()->with('success', 'Telah ditetapkan sebagai Jawaban Terbaik! (+15 Poin untuk Penulis Komentar)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    /**
     * Join Project / Committee / Volunteer Charity
     */
    public function join(Request $request, ForumThread $thread)
    {
        $user = Auth::user();

        if ($thread->status === 'completed' || $thread->is_locked) {
            return back()->with('error', 'Aktivitas kolaborasi ini sudah selesai atau ditutup.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Check if already applied
            $existing = ForumMember::where('forum_thread_id', $thread->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                return back()->with('error', 'Anda sudah mengajukan pendaftaran sebelumnya.');
            }

            $member = ForumMember::create([
                'forum_thread_id' => $thread->id,
                'user_id' => $user->id,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Immediate incentive for joining: +10 points to member
            ReputationLog::log($user->id, 10, 'forum_join', "Mendaftar dalam tim/kegiatan: {$thread->title}", $member);

            DB::commit();

            return back()->with('success', 'Permintaan pendaftaran berhasil dikirim! (+10 Poin Reputasi)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Approve Member Registration
     */
    public function approveMember(ForumMember $member)
    {
        $user = Auth::user();
        $thread = $member->thread;

        // Author or teacher/admin can approve
        if ($user->id !== $thread->user_id && !$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Unauthorized');
        }

        DB::beginTransaction();
        try {
            $member->update(['status' => 'approved']);

            // Author gets +5 points for approving a team member
            ReputationLog::log($thread->user_id, 5, 'forum_team_approve', "Menyetujui anggota tim baru pada: {$thread->title}", $member);

            DB::commit();

            return back()->with('success', 'Anggota tim berhasil disetujui! (+5 Poin Penulis)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * Reject Member Registration
     */
    public function rejectMember(ForumMember $member)
    {
        $user = Auth::user();
        $thread = $member->thread;

        if ($user->id !== $thread->user_id && !$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Unauthorized');
        }

        DB::beginTransaction();
        try {
            $member->update(['status' => 'rejected']);

            // Remove the +10 points the applicant originally got for joining
            ReputationLog::removeLog($member->user_id, ForumMember::class, $member->id);

            DB::commit();

            return back()->with('success', 'Pendaftaran anggota ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses penolakan: ' . $e->getMessage());
        }
    }

    /**
     * Update Collaboration Lifecycle Status
     */
    public function updateStatus(Request $request, ForumThread $thread)
    {
        $user = Auth::user();

        if ($user->id !== $thread->user_id && !$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:seeking_members,active,completed',
        ]);

        $oldStatus = $thread->status;
        $newStatus = $validated['status'];

        DB::beginTransaction();
        try {
            $thread->update(['status' => $newStatus]);

            // Completion reward: +50 points to creator and approved members
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                // Award points to creator
                ReputationLog::log($thread->user_id, 50, 'forum_completed_project', "Menyelesaikan kolaborasi proyek/kegiatan: {$thread->title}", $thread);
                
                // Award points to all approved team members
                $approvedMembers = $thread->approvedMembers()->get();
                foreach ($approvedMembers as $member) {
                    ReputationLog::log($member->user_id, 50, 'forum_completed_project', "Menyelesaikan kolaborasi proyek/kegiatan: {$thread->title}", $thread);
                }
            }

            DB::commit();

            return back()->with('success', 'Status kolaborasi diperbarui!' . ($newStatus === 'completed' ? ' (+50 Poin Bonus Penyelesaian dibagikan!)' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Update Donation progress manually (Charity categories)
     */
    public function donate(Request $request, ForumThread $thread)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $thread->increment('charity_current_amount', $validated['amount']);

            // Reward donor: +10 points for donating
            ReputationLog::log($user->id, 10, 'forum_charity_donation', "Berdonasi pada aksi sosial: {$thread->title}", $thread);

            DB::commit();

            return back()->with('success', 'Terima kasih atas partisipasi donasi Anda! (+10 Poin Reputasi)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses donasi: ' . $e->getMessage());
        }
    }

    /**
     * Edit Thread View
     */
    public function edit(ForumThread $thread)
    {
        $user = Auth::user();

        if ($user->id !== $thread->user_id && !$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Unauthorized');
        }

        $badges = $user->badges()->get();
        
        $cbtResults = collect([]);
        if ($user->isSiswa() && $user->student) {
            $cbtResults = CbtExamResult::where('student_id', $user->student->id)
                ->with('exam')
                ->latest()
                ->take(10)
                ->get();
        }

        return view('forum.edit', compact('thread', 'badges', 'cbtResults'));
    }

    /**
     * Update Thread
     */
    public function update(Request $request, ForumThread $thread)
    {
        $user = Auth::user();

        if ($user->id !== $thread->user_id && !$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Unauthorized');
        }

        $allowedCategories = array_keys(ForumThread::CATEGORIES);
        if (!$user->isSuperAdmin() && !$user->isAdminSekolah() && !$user->isGuru()) {
            $allowedCategories = array_diff($allowedCategories, ['info']);
        }

        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|in:' . implode(',', $allowedCategories),
            'image' => 'nullable|image|max:5120', // 5MB limit
            'attachment' => 'nullable|file|max:10240', // 10MB limit
            
            // Collaboration & Charity Specifics
            'charity_target_amount' => 'nullable|numeric|min:0',
            'charity_target_volunteers' => 'nullable|integer|min:0',
            'recruitment_enabled' => 'nullable|boolean',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $thread->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
            ]);

            // Handle uploads
            if ($request->hasFile('image')) {
                // Delete old image
                if ($thread->image_path) {
                    \Storage::disk('public')->delete($thread->image_path);
                }
                $path = $request->file('image')->store('forum', 'public');
                $thread->update(['image_path' => $path]);
            }

            if ($request->hasFile('attachment')) {
                // Delete old attachment
                if ($thread->attachment_path) {
                    \Storage::disk('public')->delete($thread->attachment_path);
                }
                $file = $request->file('attachment');
                $path = $file->store('forum_attachments', 'public');
                $thread->update([
                    'attachment_path' => $path,
                    'attachment_name' => $file->getClientOriginalName()
                ]);
            }

            // Reference linking (Badges / Grades)
            $perfCategories = ['performance', 'art_gallery', 'talent', 'portfolio'];
            if (in_array($validated['category'], $perfCategories) && !empty($validated['reference_type']) && !empty($validated['reference_id'])) {
                if ($validated['reference_type'] === 'badge') {
                    $thread->update([
                        'reference_type' => \App\Models\Badge::class,
                        'reference_id' => $validated['reference_id']
                    ]);
                } elseif ($validated['reference_type'] === 'grade') {
                    $thread->update([
                        'reference_type' => \App\Models\CbtExamResult::class,
                        'reference_id' => $validated['reference_id']
                    ]);
                }
            } else {
                $thread->update([
                    'reference_type' => null,
                    'reference_id' => null
                ]);
            }

            // Charity data
            if ($validated['category'] === 'charity') {
                $thread->update([
                    'charity_target_amount' => $validated['charity_target_amount'] ?? null,
                    'charity_target_volunteers' => $validated['charity_target_volunteers'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('forum.show', $thread)
                ->with('success', 'Topik diskusi berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui postingan: ' . $e->getMessage());
        }
    }

    /**
     * Delete Thread
     */
    public function destroy(ForumThread $thread)
    {
        $user = Auth::user();

        if ($user->id !== $thread->user_id && !$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Unauthorized');
        }

        DB::beginTransaction();
        try {
            // Revert creator points
            ReputationLog::removeLog($thread->user_id, ForumThread::class, $thread->id);

            // Revert all reply and like logs for this thread
            $replies = $thread->replies()->get();
            foreach ($replies as $reply) {
                ReputationLog::removeLog($reply->user_id, ForumReply::class, $reply->id);
            }

            $likes = $thread->likes()->get();
            foreach ($likes as $like) {
                ReputationLog::removeLog($like->user_id, ForumLike::class, $like->id);
            }

            // Remove files
            if ($thread->image_path) {
                Storage::disk('public')->delete($thread->image_path);
            }
            if ($thread->attachment_path) {
                Storage::disk('public')->delete($thread->attachment_path);
            }

            $thread->delete();

            DB::commit();

            return redirect()->route('forum.index')
                ->with('success', 'Topik diskusi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus topik: ' . $e->getMessage());
        }
    }
}
