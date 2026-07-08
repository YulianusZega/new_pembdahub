<?php

namespace App\Http\Requests\Cbt;

use Illuminate\Foundation\Http\FormRequest;

class GradeEssayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'manual_score' => 'required|numeric|min:0',
            'teacher_feedback' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'manual_score.required' => 'Skor wajib diisi.',
            'manual_score.min' => 'Skor minimal 0.',
        ];
    }
}
