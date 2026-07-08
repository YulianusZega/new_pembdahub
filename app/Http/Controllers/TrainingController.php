<?php

namespace App\Http\Controllers;

use App\Models\TrainingModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role;

        $query = TrainingModule::published()
            ->forRole($role)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $modules = $query->paginate(9)->withQueryString();
        $layout = auth()->user()->layout;

        return view('training.index', compact('modules', 'layout'));
    }

    public function show(TrainingModule $trainingModule)
    {
        $role = auth()->user()->role;

        if (!$trainingModule->is_published || !in_array($role, $trainingModule->target_roles ?? [])) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $layout = auth()->user()->layout;

        return view('training.show', ['module' => $trainingModule, 'layout' => $layout]);
    }

    public function download(TrainingModule $trainingModule)
    {
        if (!$trainingModule->is_published) {
            abort(403, 'Materi pelatihan tidak tersedia atau belum dipublikasikan.');
        }

        if (!$trainingModule->pdf_file || !Storage::disk('public')->exists($trainingModule->pdf_file)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        return Storage::disk('public')->download($trainingModule->pdf_file, $trainingModule->title . '.pdf');
    }
}
