<?php

namespace App\Http\Requests;

use App\Models\Aset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePenghapusanAsetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi input penghapusan aset.
     * Format body yang diharapkan:
     * {
     *   "kode_inventaris": "INV-2026-001",
     *   "tanggal_hapus": "2026-04-16",
     *   "alasan_hapus": "Sudah tidak layak pakai",
     *   "id_penyetuju": 2
     * }
     */
    public function rules(): array
    {
        return [
            'kode_barang' => ['required', 'string', 'exists:aset,kode_barang'],
            'tanggal_hapus'   => ['required', 'date'],
            'alasan_hapus'    => ['required', 'string'],
            'id_penyetuju'    => ['required', 'integer', 'exists:pengguna,id_pengguna'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.exists'   => 'Aset tidak ditemukan.',
            'tanggal_hapus.required'   => 'Tanggal penghapusan wajib diisi.',
            'tanggal_hapus.date'       => 'Format tanggal penghapusan tidak valid.',
            'alasan_hapus.required'    => 'Alasan penghapusan wajib diisi.',
            'id_penyetuju.required'    => 'Penyetuju wajib dipilih.',
            'id_penyetuju.exists'      => 'Penyetuju tidak ditemukan.',
        ];
    }

    /**
     * Validasi tambahan:
     * 1. Aset tidak boleh sudah berstatus "Dihapus".
     * 2. Aset tidak boleh sedang "Dipinjam".
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $aset = Aset::find($this->input('kode_barang'));

            if (!$aset) {
                return;
            }

            if ($aset->status === 'Dihapus') {
                $validator->errors()->add(
                    'kode_barang',
                    "Aset '{$aset->kode_barang}' sudah berstatus Dihapus."
                );
            }

            if ($aset->status === 'Dipinjam') {
                $validator->errors()->add(
                    'kode_barang',
                    "Aset '{$aset->kode_barang}' sedang dipinjam dan tidak dapat dihapuskan."
                );
            }
        });
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
