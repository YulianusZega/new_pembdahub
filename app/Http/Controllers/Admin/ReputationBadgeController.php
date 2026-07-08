<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;

class ReputationBadgeController extends Controller
{
    /**
     * List all badges
     */
    public function index()
    {
        $badges = Badge::withCount('users')->orderBy('requirement_value')->get();
        return view('admin.reputation.badges.index', compact('badges'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.reputation.badges.create');
    }

    /**
     * Store new badge
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:badges,code',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'description' => 'required|string',
            'requirement_type' => 'required|in:points,attendance,quiz,other',
            'requirement_value' => 'required|integer|min:0',
        ]);

        Badge::create($validated);

        return redirect()->route('admin.reputation.badges.index')
            ->with('success', 'Lencana berhasil dibuat.');
    }

    /**
     * Show edit form
     */
    public function edit(Badge $badge)
    {
        return view('admin.reputation.badges.edit', compact('badge'));
    }

    /**
     * Update badge
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:badges,code,' . $badge->id,
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'description' => 'required|string',
            'requirement_type' => 'required|in:points,attendance,quiz,other',
            'requirement_value' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $badge->update($validated);

        return redirect()->route('admin.reputation.badges.index')
            ->with('success', 'Lencana berhasil diperbarui.');
    }

    /**
     * Delete badge
     */
    public function destroy(Badge $badge)
    {
        if ($badge->users()->count() > 0) {
            return back()->with('error', 'Lencana tidak dapat dihapus karena sudah dimiliki oleh beberapa pengguna.');
        }

        $badge->delete();

        return redirect()->route('admin.reputation.badges.index')
            ->with('success', 'Lencana berhasil dihapus.');
    }
}
