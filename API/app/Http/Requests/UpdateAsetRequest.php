<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAsetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_barang'        => ['sometimes', 'string', 'max:50', 'unique:aset,kode_barang,' . $this->route('id') . ',kode_barang'],
            'id_master_barang'   => ['sometimes', 'integer', 'exists:master_barang,id_master_barang'],
            'tanggal_registrasi' => ['sometimes', 'date'],
            'gambar'             => ['nullable', 'string'],
            'keterangan'         => ['nullable', 'string'],
            'status'             => ['sometimes', 'required', 'string'],
            'id_pengadaan'       => ['nullable', 'integer', 'exists:pengadaan,id_pengadaan'],
            'id_kondisi'         => ['nullable', 'integer', 'exists:kondisi,id_kondisi'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
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
