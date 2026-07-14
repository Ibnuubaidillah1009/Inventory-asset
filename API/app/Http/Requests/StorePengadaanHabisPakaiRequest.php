<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePengadaanHabisPakaiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_inventaris'   => ['required', 'string', 'max:50', 'unique:pengadaan_habis_pakai,kode_inventaris'],
            'tanggal_pengadaan' => ['required', 'date'],
            'kode_barang'       => ['required', 'string', 'max:50', 'exists:aset_habis_pakai,kode_barang'],
            'jumlah'            => ['nullable', 'integer', 'min:1'],
            'harga_satuan'      => ['nullable', 'numeric', 'min:0'],
            'id_pemasok'        => ['nullable', 'integer', 'exists:pemasok,id_pemasok'],
            'kode_gudang'       => ['nullable', 'string', 'max:20', 'exists:gudang,kode_gudang'],
            'keterangan'        => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422));
    }
}
