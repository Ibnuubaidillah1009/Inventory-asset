<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMasterBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_barang'  => ['required', 'string', 'max:255'],
            'id_kategori'  => ['required', 'integer', 'exists:kategori,id_kategori'],
            'id_merek'     => ['required', 'integer', 'exists:merek,id_merek'],
            'id_satuan'    => ['required', 'integer', 'exists:satuan,id_satuan'],
            'jenis_barang' => ['required', 'string', 'in:Aset,Habis Pakai'],
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
