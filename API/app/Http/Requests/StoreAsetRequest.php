<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAsetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_barang'        => ['required', 'string', 'max:50', 'unique:aset,kode_barang'],
            'id_master_barang'   => ['required', 'integer', 'exists:master_barang,id_master_barang'],
            'tanggal_registrasi' => ['required', 'date'],
            'gambar'             => ['nullable', 'string'],
            'keterangan'         => ['nullable', 'string'],
            'status'             => ['required', 'string'],
            'id_pengadaan'       => ['nullable', 'integer', 'exists:pengadaan,id_pengadaan'],
            'id_kondisi'         => ['nullable', 'integer', 'exists:kondisi,id_kondisi'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.required'         => 'Kode barang wajib diisi.',
            'kode_barang.unique'           => 'Kode barang sudah digunakan.',
            'id_master_barang.required'    => 'Master barang wajib dipilih.',
            'id_master_barang.exists'      => 'Master barang tidak ditemukan.',
            'status.required'              => 'Status aset wajib diisi.',
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
