<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => 'required|exists:schools,id',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'is_active' => 'boolean',
            'employee_type' => 'required|in:staff_tu,staff_keuangan,security,cleaning_service,driver,other',
            'employment_status' => 'required|in:yayasan,pns,pppk,honorer,percobaan,magang,kontrak',
            'tmt_date' => 'required|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'marital_status' => 'required|in:menikah,belum_menikah',
            'children_count' => 'nullable|integer|min:0|max:10',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required' => 'Sekolah wajib dipilih.',
            'employee_code.required' => 'Kode pegawai wajib diisi.',
            'employee_code.unique' => 'Kode pegawai sudah digunakan.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'employee_type.required' => 'Jenis pegawai wajib dipilih.',
            'employment_status.required' => 'Status kepegawaian wajib dipilih.',
            'tmt_date.required' => 'TMT wajib diisi.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
