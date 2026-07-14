<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePengadaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_pengadaan'         => ['nullable', 'date'],
            'total_harga'               => ['nullable', 'numeric', 'min:0'],
            'keterangan'                => ['nullable', 'string'],
            'kode_gudang'               => ['nullable', 'string', 'exists:gudang,kode_gudang'],
            'jumlah_pengadaan'          => ['nullable', 'integer', 'min:1'],
            'id_sumber_perolehan'       => ['nullable', 'integer', 'exists:sumber_perolehan,id_sumber_perolehan'],
            'status'                    => ['nullable', 'in:diproses,dibelanjakan,selesai'],
            'permintaan'                => ['nullable', 'array'],
            'permintaan.*'              => ['string', 'exists:permintaan,kode_permintaan'],
            'detail'                    => ['nullable', 'array', 'min:1'],
            'detail.*.id_master_barang' => ['required_with:detail', 'integer', 'exists:master_barang,id_master_barang'],
            'detail.*.jumlah_masuk'     => ['required_with:detail', 'integer', 'min:1'],
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
