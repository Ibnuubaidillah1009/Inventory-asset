<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAsetHabisPakaiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_barang'        => ['required', 'string', 'max:50', 'unique:aset_habis_pakai,kode_barang'],
            'id_master_barang'   => ['required', 'integer', 'exists:master_barang,id_master_barang'],
            'tanggal_registrasi' => ['required', 'date'],
            'stok'               => ['nullable', 'integer', 'min:0'],
            'keterangan'         => ['nullable', 'string'],
            'status'             => ['nullable', 'string'],
            'id_pengadaan'       => ['nullable', 'integer', 'exists:pengadaan,id_pengadaan'],
            'id_kondisi'         => ['nullable', 'integer', 'exists:kondisi,id_kondisi'],
            'is_returnable'      => ['nullable', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422));
    }
}
