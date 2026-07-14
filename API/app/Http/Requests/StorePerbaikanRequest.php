<?php

namespace App\Http\Requests;

use App\Models\Kerusakan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePerbaikanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi input perbaikan aset.
     * Format body yang diharapkan:
     * {
     *   "id_kerusakan": 3,
     *   "tanggal_perbaikan": "2026-04-17",
     *   "teknisi": "CV Teknik Jaya",
     *   "biaya_perbaikan": 500000,
     *   "tindakan_perbaikan": "Ganti layar LCD"
     * }
     */
    public function rules(): array
    {
        return [
            'id_kerusakan'        => ['required', 'integer', 'exists:kerusakan,id_kerusakan'],
            'tanggal_perbaikan'   => ['required', 'date'],
            'teknisi'             => ['nullable', 'string', 'max:150'],
            'biaya_perbaikan'     => ['nullable', 'numeric', 'min:0'],
            'tindakan_perbaikan'  => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_kerusakan.required'        => 'ID kerusakan wajib diisi.',
            'id_kerusakan.exists'          => 'Data kerusakan tidak ditemukan.',
            'tanggal_perbaikan.required'   => 'Tanggal perbaikan wajib diisi.',
            'tanggal_perbaikan.date'       => 'Format tanggal perbaikan tidak valid.',
            'biaya_perbaikan.numeric'      => 'Biaya perbaikan harus berupa angka.',
            'biaya_perbaikan.min'          => 'Biaya perbaikan tidak boleh negatif.',
            'tindakan_perbaikan.required'  => 'Tindakan perbaikan wajib diisi.',
        ];
    }

    /**
     * Validasi tambahan:
     * Kerusakan yang dirujuk tidak boleh sudah berstatus "Selesai".
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // Cek status kerusakan
            $kerusakan = Kerusakan::find($this->input('id_kerusakan'));

            if ($kerusakan && $kerusakan->status_kerusakan === 'Selesai') {
                $validator->errors()->add(
                    'id_kerusakan',
                    'Kerusakan ini sudah selesai diperbaiki.'
                );
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
