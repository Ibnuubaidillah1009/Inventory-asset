<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLemariRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_lemari' => ['required', 'string', 'max:50', 'unique:lemari,kode_lemari'],
            'nama'        => ['required', 'string', 'max:100'],
            'id_ruang'    => ['required', 'integer', 'exists:ruang,id_ruang'],
            'nomor_rak'   => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_lemari.required' => 'Kode lemari wajib diisi.',
            'kode_lemari.unique'   => 'Kode lemari sudah digunakan.',
            'nama.required'        => 'Nama lemari wajib diisi.',
            'id_ruang.required'    => 'Ruang wajib dipilih.',
            'id_ruang.exists'      => 'Ruang tidak ditemukan.',
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
