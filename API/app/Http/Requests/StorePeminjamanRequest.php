<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_peminjaman'       => ['required', 'string', 'max:50', 'unique:peminjaman,nomor_peminjaman'],
            'tanggal_pinjam'         => ['required', 'date'],
            'nama_peminjam'          => ['required', 'string', 'max:150'],
            'nomor_telepon'          => ['nullable', 'string', 'max:20'],
            'lama_pinjam_hari'       => ['required', 'integer', 'min:1'],
            'keterangan'             => ['nullable', 'string'],
            'detail'                 => ['required', 'array', 'min:1'],
            'detail.*.kode_barang'   => ['required', 'string', 'exists:aset,kode_barang'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_peminjaman.required'     => 'Nomor peminjaman wajib diisi.',
            'nomor_peminjaman.unique'       => 'Nomor peminjaman sudah ada.',
            'tanggal_pinjam.required'       => 'Tanggal pinjam wajib diisi.',
            'nama_peminjam.required'        => 'Nama peminjam wajib diisi.',
            'lama_pinjam_hari.required'     => 'Lama pinjam (hari) wajib diisi.',
            'lama_pinjam_hari.min'          => 'Lama pinjam minimal 1 hari.',
            'detail.required'               => 'Detail peminjaman wajib diisi.',
            'detail.min'                    => 'Minimal harus ada 1 item yang dipinjam.',
            'detail.*.kode_barang.required' => 'Kode barang wajib diisi untuk setiap item.',
            'detail.*.kode_barang.exists'   => 'Aset tidak ditemukan.',
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
