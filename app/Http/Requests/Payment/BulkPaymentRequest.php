<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class BulkPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bill_ids' => 'required|array|min:1',
            'bill_ids.*' => 'exists:student_bills,id',
            'payment_method' => 'required|in:cash,transfer,qris,card',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'bill_ids.required' => 'Pilih minimal 1 tagihan.',
            'bill_ids.min' => 'Pilih minimal 1 tagihan.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
        ];
    }
}
