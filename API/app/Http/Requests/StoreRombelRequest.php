<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRombelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_rombel' => ['required', 'string', 'max:100'],
            'id_jurusan'  => ['required', 'integer', 'exists:jurusan,id_jurusan'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_rombel.required' => 'Nama rombel wajib diisi.',
            'id_jurusan.required'  => 'Jurusan wajib dipilih.',
            'id_jurusan.exists'    => 'Jurusan tidak ditemukan.',
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
