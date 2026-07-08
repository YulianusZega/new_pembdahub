<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreParentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'relation_type' => 'required|in:ayah,ibu,wali',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'create_account' => 'boolean',
            'account_email' => 'required_if:create_account,1|nullable|email|unique:users,email',
            'password' => ['required_if:create_account,1', 'nullable', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa wajib dipilih.',
            'relation_type.required' => 'Jenis hubungan wajib dipilih.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'account_email.required_if' => 'Email akun wajib diisi jika membuat akun.',
            'account_email.unique' => 'Email sudah digunakan.',
            'password.required_if' => 'Password wajib diisi jika membuat akun.',
        ];
    }
}
