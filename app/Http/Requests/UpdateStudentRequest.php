<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentId = $this->route('student')->id;

        return [
            'school_id' => 'required|exists:schools,id',
            'nisn' => "required|string|max:20|unique:students,nisn,{$studentId}",
            'nis' => 'nullable|string|max:20',
            'full_name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date|before:today',
            'religion' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'previous_school' => 'nullable|string|max:100',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_occupation' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string',
            'hobby' => 'nullable|string|max:255',
            'health_history' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:aktif,lulus,keluar,pindah',
            'rfid_uid' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'school_id.required' => 'Sekolah harus dipilih.',
            'nisn.required' => 'NISN harus diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'full_name.required' => 'Nama lengkap harus diisi.',
            'gender.required' => 'Jenis kelamin harus dipilih.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
