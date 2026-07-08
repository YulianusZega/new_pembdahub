<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class BatchPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bill_ids' => 'required|string',
            'student_id' => 'required|exists:students,id',
            'payment_method' => 'required|in:cash,transfer,qris,card,check',
            'reference_number' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'bill_ids.required' => 'Pilih minimal 1 tagihan.',
            'student_id.required' => 'Siswa wajib dipilih.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
        ];
    }
}
