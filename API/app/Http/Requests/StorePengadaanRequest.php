<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePengadaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_pengadaan'        => ['required', 'date'],
            'total_harga'              => ['nullable', 'numeric', 'min:0'],
            'keterangan'               => ['nullable', 'string'],
            'kode_gudang'              => ['nullable', 'string', 'exists:gudang,kode_gudang'],
            'jumlah_pengadaan'         => ['nullable', 'integer', 'min:1'],
            'id_sumber_perolehan'      => ['nullable', 'integer', 'exists:sumber_perolehan,id_sumber_perolehan'],
            'permintaan'               => ['nullable', 'array'],
            'permintaan.*'             => ['string', 'exists:permintaan,kode_permintaan'],
            'detail'                   => ['required', 'array', 'min:1'],
            'detail.*.id_master_barang' => ['required', 'integer', 'exists:master_barang,id_master_barang'],
            'detail.*.jumlah_masuk'     => ['required', 'integer', 'min:1'],
            'detail.*.harga_satuan'     => ['nullable', 'numeric', 'min:0'],
            'detail.*.pemasok'          => ['nullable', 'string', 'max:255'],
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
