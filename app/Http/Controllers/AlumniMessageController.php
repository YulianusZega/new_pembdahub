<?php

namespace App\Http\Controllers;

use App\Models\AlumniMessage;
use App\Models\User;
use Illuminate\Http\Request;

class AlumniMessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $schoolId = $user->alumniDirectory->school_id ?? null;

        if (!$schoolId) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil daftar alumni di sekolah yang sama, kecuali diri sendiri
        $alumnis = User::whereHas('alumniDirectory', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->where('id', '!=', $user->id)->get();

        return view('alumni.chat.index', compact('alumnis'));
    }

    public function show(User $contact)
    {
        $user = auth()->user();
        $schoolId = $user->alumniDirectory->school_id ?? null;
        $contactSchoolId = $contact->alumniDirectory->school_id ?? null;

        if ($schoolId !== $contactSchoolId) {
            abort(403, 'Anda hanya dapat mengirim pesan ke alumni dari unit sekolah yang sama.');
        }

        // Mark as read
        AlumniMessage::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->update(['is_read' => true]);

        $messages = AlumniMessage::where(function($q) use ($user, $contact) {
            $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
        })->orWhere(function($q) use ($user, $contact) {
            $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        // Pass to layout
        $alumnis = User::whereHas('alumniDirectory', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->where('id', '!=', $user->id)->get();

        return view('alumni.chat.index', compact('contact', 'messages', 'alumnis'));
    }

    public function store(Request $request, User $contact)
    {
        $request->validate(['message' => 'required']);

        $user = auth()->user();
        $schoolId = $user->alumniDirectory->school_id ?? null;
        $contactSchoolId = $contact->alumniDirectory->school_id ?? null;

        if ($schoolId !== $contactSchoolId) {
            abort(403, 'Akses ditolak.');
        }

        AlumniMessage::create([
            'sender_id' => $user->id,
            'receiver_id' => $contact->id,
            'message' => $request->message,
        ]);

        return back();
    }
}
