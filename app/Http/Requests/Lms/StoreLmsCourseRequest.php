<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class StoreLmsCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:200',
            'subject_id' => 'required|exists:subjects,id',
            'semester_id' => 'required|exists:semesters,id',
            'description' => 'nullable|string',
            'classroom_ids' => 'nullable|array',
            'classroom_ids.*' => 'exists:classrooms,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama course wajib diisi.',
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
        ];
    }
}
