<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'grade_type' => 'required|in:tugas,uts,uas,sikap',
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa harus dipilih.',
            'student_id.exists' => 'Siswa tidak ditemukan.',
            'subject_id.required' => 'Mata pelajaran harus dipilih.',
            'subject_id.exists' => 'Mata pelajaran tidak ditemukan.',
            'teacher_id.required' => 'Guru harus dipilih.',
            'teacher_id.exists' => 'Guru tidak ditemukan.',
            'grade_type.required' => 'Tipe nilai harus dipilih.',
            'grade_type.in' => 'Tipe nilai tidak valid.',
            'score.required' => 'Nilai harus diisi.',
            'score.numeric' => 'Nilai harus berupa angka.',
            'score.min' => 'Nilai minimal adalah 0.',
            'score.max' => 'Nilai maksimal adalah 100.',
        ];
    }
}
