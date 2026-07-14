<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_master_barang'     => ['required', 'integer', 'exists:master_barang,id_master_barang'],
            'jumlah_diminta'       => ['required', 'integer', 'min:1'],
            'alasan_kebutuhan'     => ['required', 'string'],
            'keterangan_keperluan' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_master_barang.required' => 'Barang wajib dipilih.',
            'id_master_barang.exists'   => 'Barang tidak valid.',
            'jumlah_diminta.required'   => 'Jumlah diminta wajib diisi.',
            'jumlah_diminta.min'        => 'Jumlah minimal 1.',
            'alasan_kebutuhan.required' => 'Alasan kebutuhan wajib diisi.',
        ];
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
