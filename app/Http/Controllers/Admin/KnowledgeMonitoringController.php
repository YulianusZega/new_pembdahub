<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeMaterial;
use App\Models\Teacher;
use Illuminate\Http\Request;

class KnowledgeMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $role = auth()->user()->role;
        $isGlobalView = in_array($role, ['super_admin', 'yayasan', 'ketua_yayasan']);

        $materialsQuery = KnowledgeMaterial::query();
        $teachersQuery = Teacher::query();

        if (!$isGlobalView && $schoolId) {
            $materialsQuery->whereHas('teacher', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
            $teachersQuery->where('school_id', $schoolId);
        }

        $totalMaterials = (clone $materialsQuery)->count();
        $totalViews = (clone $materialsQuery)->sum('views_count');
        $totalLikes = (clone $materialsQuery)->sum('likes_count');
        $totalBookmarks = (clone $materialsQuery)->sum('bookmarks_count');
        $totalDownloads = (clone $materialsQuery)->sum('downloads_count');

        // Leaderboard & Teacher Stats
        $teachers = $teachersQuery->with(['school', 'knowledgeMaterials'])->get();

        $teacherLeaderboard = $teachers->map(function ($teacher) {
            $materials = $teacher->knowledgeMaterials;
            $uploads = $materials->count();
            $likes = $materials->sum('likes_count');
            $bookmarks = $materials->sum('bookmarks_count');
            $views = $materials->sum('views_count');
            $downloads = $materials->sum('downloads_count');

            $points = $materials->reduce(function ($carry, $item) {
                return $carry + $item->points;
            }, 0);

            return (object) [
                'teacher' => $teacher,
                'uploads' => $uploads,
                'likes' => $likes,
                'bookmarks' => $bookmarks,
                'views' => $views,
                'downloads' => $downloads,
                'points' => $points,
            ];
        })->sortByDesc('points')->values();

        // Recent Uploads
        $recentUploads = (clone $materialsQuery)
            ->with(['teacher', 'subject'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.knowledge.monitoring', compact(
            'totalMaterials',
            'totalViews',
            'totalLikes',
            'totalBookmarks',
            'totalDownloads',
            'teacherLeaderboard',
            'recentUploads'
        ));
    }

    /**
     * Delete material as Admin
     */
    public function destroy(KnowledgeMaterial $knowledge)
    {
        if ($knowledge->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($knowledge->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($knowledge->file_path);
        }

        if ($knowledge->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($knowledge->thumbnail_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($knowledge->thumbnail_path);
        }

        $knowledge->delete();

        return back()->with('success', 'Materi berhasil dihapus dari sistem.');
    }
}
