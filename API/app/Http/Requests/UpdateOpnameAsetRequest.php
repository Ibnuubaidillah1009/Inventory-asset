<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOpnameAsetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_inventaris' => ['sometimes', 'required', 'string', 'max:50', 'exists:pengadaan,kode_inventaris'],
            'tanggal_opname'  => ['sometimes', 'required', 'date'],
            'id_kondisi'      => ['nullable', 'integer', 'exists:kondisi,id_kondisi'],
            'keterangan'      => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_inventaris.exists' => 'Kode inventaris tidak ditemukan di data pengadaan.',
            'id_kondisi.exists'      => 'Kondisi tidak ditemukan.',
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
