<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreGudangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_gudang'  => ['required', 'string', 'max:20', 'unique:gudang,kode_gudang'],
            'nama_gudang'  => ['required', 'string', 'max:100'],
            'keterangan'   => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_gudang.required' => 'Kode gudang wajib diisi.',
            'kode_gudang.unique'   => 'Kode gudang sudah digunakan.',
            'nama_gudang.required' => 'Nama gudang wajib diisi.',
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
