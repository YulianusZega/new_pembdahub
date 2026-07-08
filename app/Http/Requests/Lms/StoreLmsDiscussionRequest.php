<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class StoreLmsDiscussionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:300',
            'content' => 'required|string',
            'type' => 'required|in:discussion,question,announcement',
            'is_pinned' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul diskusi wajib diisi.',
            'title.max' => 'Judul diskusi maksimal 300 karakter.',
            'content.required' => 'Konten diskusi wajib diisi.',
            'type.required' => 'Tipe diskusi wajib dipilih.',
            'type.in' => 'Tipe diskusi tidak valid.',
        ];
    }
}
