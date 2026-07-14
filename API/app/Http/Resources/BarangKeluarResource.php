<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangKeluarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'no_transaksi'    => $this->no_transaksi,
            'tanggal_keluar'  => $this->tanggal_keluar ? $this->tanggal_keluar->format('Y-m-d') : null,
            'kode_inventaris' => $this->kode_inventaris,
            'jumlah_keluar'   => $this->jumlah_keluar,
            'keterangan'      => $this->keterangan,
            'id_ruang'        => $this->id_ruang,
            'nama_penerima'   => $this->nama_penerima,
            'petugas'         => $this->petugas,
            'kode_gudang'     => $this->kode_gudang,
            // Relasi (Load jika ada)
            'pengadaan_habis_pakai' => $this->whenLoaded('pengadaanHabisPakai', function () {
                return [
                    'kode_inventaris' => $this->pengadaanHabisPakai->kode_inventaris,
                    'kode_barang'     => $this->pengadaanHabisPakai->kode_barang,
                    'nama_barang'     => $this->pengadaanHabisPakai->asetHabisPakai ? $this->pengadaanHabisPakai->asetHabisPakai->nama_barang : null,
                ];
            }),
            'ruang'           => $this->whenLoaded('ruang'),
            'penerima'        => $this->whenLoaded('penerima', function () {
                return [
                    'id_pengguna' => $this->penerima->id_pengguna,
                    'username'    => $this->penerima->username,
                ];
            }),
            'petugas_user'    => $this->whenLoaded('petugasUser', function () {
                return [
                    'id_pengguna' => $this->petugasUser->id_pengguna,
                    'username'    => $this->petugasUser->username,
                ];
            }),
            'gudang'          => $this->whenLoaded('gudang'),
        ];
    }
}
