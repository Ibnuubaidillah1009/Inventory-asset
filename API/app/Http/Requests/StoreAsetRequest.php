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
            'kode_barang'                        => ['required', 'string', 'max:50', 'unique:aset,kode_barang'],
            'id_master_barang'                   => ['required', 'integer', 'exists:master_barang,id_master_barang'],
            'id_jurusan'                         => ['nullable', 'integer', 'exists:jurusan,id_jurusan'],
            'penanggung_jawab'                   => ['nullable', 'string', 'max:150'],
            'id_ruang'                           => ['nullable', 'integer', 'exists:ruang,id_ruang'],
            'id_lokasi'                          => ['nullable', 'integer', 'exists:lokasi,id_lokasi'],
            'no_seri'                            => ['nullable', 'string', 'max:100'],
            'model_tipe'                         => ['nullable', 'string', 'max:150'],
            'barcode'                            => ['nullable', 'string', 'max:100', 'unique:aset,barcode'],
            'harga_satuan'                       => ['required', 'numeric', 'min:0'],
            'nilai_residu'                       => ['nullable', 'numeric', 'min:0'],
            'umur_ekonomi'                       => ['nullable', 'integer', 'min:0'],
            'kode_inventaris'                    => ['nullable', 'string', 'max:50', 'unique:aset,kode_inventaris'],
            'status'                             => ['required', 'string'],
            'id_kondisi'                         => ['nullable', 'integer', 'exists:kondisi,id_kondisi'],
            'tanggal_registrasi'                 => ['nullable', 'date'],
            'gambar'                             => ['nullable', 'string'],
            'keterangan'                         => ['nullable', 'string'],
            'metode_penyusutan'                  => ['nullable', 'string', 'in:Garis Lurus,Saldo Menurun,Unit Produksi,Lainnya'],
            'tanggal_garansi_mulai'              => ['nullable', 'date'],
            'tanggal_garansi_akhir'              => ['nullable', 'date', 'after_or_equal:tanggal_garansi_mulai'],
            'info_garansi'                       => ['nullable', 'string'],
            'jadwal_pemeliharaan_berikutnya'     => ['nullable', 'date'],
            'nomor_polis_asuransi'               => ['nullable', 'string', 'max:100'],
            'nilai_pertanggungan'                => ['nullable', 'numeric', 'min:0'],
            'tanggal_akhir_asuransi'             => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.required'               => 'Kode barang wajib diisi.',
            'kode_barang.unique'                 => 'Kode barang sudah digunakan.',
            'barcode.unique'                     => 'Barcode sudah digunakan.',
            'id_master_barang.required'          => 'Master barang wajib dipilih.',
            'id_master_barang.exists'            => 'Master barang tidak ditemukan.',
            'harga_satuan.required'              => 'Harga satuan wajib diisi.',
            'status.required'                    => 'Status aset wajib diisi.',
            'tanggal_garansi_akhir.after_or_equal' => 'Tanggal akhir garansi harus setelah atau sama dengan tanggal mulai garansi.',
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
