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
            'id_pemasok'              => ['nullable', 'integer', 'exists:pemasok,id_pemasok'],
            'nomor_po'                => ['nullable', 'string', 'max:100', 'unique:pengadaan,nomor_po'],
            'nomor_faktur'            => ['nullable', 'string', 'max:100'],
            'tanggal_pengadaan'       => ['required', 'date'],
            'total_harga'             => ['nullable', 'numeric', 'min:0'],
            'persentase_ppn'          => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nominal_ppn'             => ['nullable', 'numeric', 'min:0'],
            'grand_total'             => ['nullable', 'numeric', 'min:0'],
            'keterangan'              => ['nullable', 'string'],
            'kode_gudang'             => ['nullable', 'string'],
            'id_sumber_perolehan'     => ['nullable', 'integer', 'exists:sumber_perolehan,id_sumber_perolehan'],
            'id_kondisi'              => ['nullable', 'integer', 'exists:kondisi,id_kondisi'],
            'tanggal_pengiriman'      => ['nullable', 'date'],
            'nomor_po_lampiran'       => ['nullable', 'string', 'max:255'],
            'status'                  => ['nullable', 'in:Menunggu Proses,Dibelanjakan,Selesai'],
            'permintaan'              => ['nullable', 'array'],
            'permintaan.*'            => ['string', 'exists:permintaan,kode_permintaan'],
            'detail'                  => ['nullable', 'array'],
            'detail.*.id_master_barang' => ['required_with:detail', 'integer', 'exists:master_barang,id_master_barang'],
            'detail.*.jumlah_masuk'     => ['required_with:detail', 'integer', 'min:1'],
            'detail.*.harga_satuan'     => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_po.unique'           => 'Nomor PO sudah digunakan.',
            'tanggal_pengadaan.required' => 'Tanggal pengadaan wajib diisi.',
            'status.in'                 => 'Status pengadaan tidak valid.',
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
