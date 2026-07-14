<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMasterBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_barang'  => ['sometimes', 'required', 'string', 'max:255'],
            'id_kategori'  => ['sometimes', 'required', 'integer', 'exists:kategori,id_kategori'],
            'id_merek'     => ['sometimes', 'required', 'integer', 'exists:merek,id_merek'],
            'id_satuan'    => ['sometimes', 'required', 'integer', 'exists:satuan,id_satuan'],
            'jenis_barang' => ['sometimes', 'required', 'string', 'in:Aset,Habis Pakai'],
            'keterangan'   => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_kategori.exists' => 'Kategori tidak ditemukan.',
            'id_merek.exists'    => 'Merek tidak ditemukan.',
            'id_satuan.exists'   => 'Satuan tidak ditemukan.',
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
