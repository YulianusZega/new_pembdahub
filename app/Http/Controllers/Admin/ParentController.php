<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreParentRequest;
use App\Http\Requests\Admin\UpdateParentRequest;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $query = ParentModel::with(['student', 'user']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by relation type
        if ($request->filled('relation_type')) {
            $query->where('relation_type', $request->relation_type);
        }

        $parents = $query->paginate(15)->withQueryString();

        return view('admin.parents.index', compact('parents'));
    }

    public function create()
    {
        $students = Student::orderBy('full_name')
            ->get();
        
        return view('admin.parents.create', compact('students'));
    }

    public function store(StoreParentRequest $request)
    {
        $validated = $request->validated();

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Create user account if requested
                $userId = null;
                if ($request->create_account && $request->account_email) {
                    $user = User::create([
                        'name' => $validated['full_name'],
                        'email' => $request->account_email,
                        'password' => Hash::make($request->password),
                        'role' => 'orang_tua',
                        'is_active' => 1,
                    ]);
                    $userId = $user->id;
                }

                $validated['user_id'] = $userId;
                ParentModel::create($validated);

                return redirect()->route('admin.parents.index')
                    ->with('success', 'Data orang tua/wali berhasil ditambahkan.');
            });
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan data orang tua: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan data. Silakan coba lagi.');
        }
    }

    public function show(ParentModel $parent)
    {
        $parent->load(['student.classroom', 'user']);
        
        return view('admin.parents.show', compact('parent'));
    }

    public function edit(ParentModel $parent)
    {
        $students = Student::orderBy('full_name')
            ->get();
        
        return view('admin.parents.edit', compact('parent', 'students'));
    }

    public function update(UpdateParentRequest $request, ParentModel $parent)
    {
        $validated = $request->validated();

        try {
            return DB::transaction(function () use ($validated, $parent) {
                $parent->update($validated);

                if ($parent->user) {
                    $parent->user->update([
                        'name' => $validated['full_name'],
                    ]);
                }

                return redirect()->route('admin.parents.index')
                    ->with('success', 'Data orang tua/wali berhasil diperbarui.');
            });
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui data orang tua: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data. Silakan coba lagi.');
        }
    }

    public function destroy(ParentModel $parent)
    {
        try {
            return DB::transaction(function () use ($parent) {
                if ($parent->user_id) {
                    User::find($parent->user_id)?->delete();
                }

                $parent->delete();

                return redirect()->route('admin.parents.index')
                    ->with('success', 'Data orang tua/wali berhasil dihapus.');
            });
        } catch (\Exception $e) {
            Log::error('Gagal menghapus data orang tua: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }
}
