<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePemasokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pemasok'    => ['nullable', 'integer', 'unique:pemasok,id_pemasok'],
            'nama_pemasok'  => ['required', 'string', 'max:150'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string'],
            'keterangan'    => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pemasok.required' => 'Nama pemasok wajib diisi.',
            'id_pemasok.unique'     => 'ID pemasok sudah digunakan.',
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
