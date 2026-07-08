<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLmsCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
            'code' => 'nullable|string|max:20|unique:lms_courses,code,' . $this->course->id,
            'classroom_ids' => 'nullable|array',
            'classroom_ids.*' => 'exists:classrooms,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama course wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }
}
