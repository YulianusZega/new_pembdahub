<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bill_id' => 'nullable|exists:student_bills,id',
            'student_id' => 'required|exists:students,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris,card,check',
            'reference_number' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa wajib dipilih.',
            'amount_paid.required' => 'Jumlah pembayaran wajib diisi.',
            'amount_paid.min' => 'Jumlah pembayaran tidak boleh negatif.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
        ];
    }
}
