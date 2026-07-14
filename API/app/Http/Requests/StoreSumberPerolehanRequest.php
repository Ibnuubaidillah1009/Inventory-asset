<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSumberPerolehanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_sumber' => ['required', 'string', 'max:50', 'unique:sumber_perolehan,kode_sumber'],
            'nama'        => ['required', 'string', 'max:100'],
            'keterangan'  => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_sumber.required' => 'Kode sumber perolehan wajib diisi.',
            'kode_sumber.unique'   => 'Kode sumber perolehan sudah digunakan.',
            'nama.required'        => 'Nama sumber perolehan wajib diisi.',
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
