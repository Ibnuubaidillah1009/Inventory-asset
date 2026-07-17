<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_barang'                        => $this->kode_barang,
            'kode_inventaris'                    => $this->kode_inventaris,
            'id_master_barang'                   => $this->id_master_barang,
            'id_jurusan'                         => $this->id_jurusan,
            'penanggung_jawab'                   => $this->penanggung_jawab,
            'id_ruang'                           => $this->id_ruang,
            'id_lokasi'                          => $this->id_lokasi,
            'no_seri'                            => $this->no_seri,
            'model_tipe'                         => $this->model_tipe,
            'barcode'                            => $this->barcode,
            'harga_satuan'                       => $this->harga_satuan,
            'nilai_residu'                       => $this->nilai_residu,
            'umur_ekonomi'                       => $this->umur_ekonomi,
            'status'                             => $this->status,
            'id_kondisi'                         => $this->id_kondisi,
            'tanggal_registrasi'                 => $this->tanggal_registrasi,
            'gambar'                             => $this->gambar,
            'keterangan'                         => $this->keterangan,
            'metode_penyusutan'                  => $this->metode_penyusutan,
            'tanggal_garansi_mulai'              => $this->tanggal_garansi_mulai?->format('Y-m-d'),
            'tanggal_garansi_akhir'              => $this->tanggal_garansi_akhir?->format('Y-m-d'),
            'info_garansi'                       => $this->info_garansi,
            'jadwal_pemeliharaan_berikutnya'     => $this->jadwal_pemeliharaan_berikutnya?->format('Y-m-d'),
            'nomor_polis_asuransi'               => $this->nomor_polis_asuransi,
            'nilai_pertanggungan'                => $this->nilai_pertanggungan,
            'tanggal_akhir_asuransi'             => $this->tanggal_akhir_asuransi?->format('Y-m-d'),
            'master_barang'                      => $this->whenLoaded('masterBarang'),
            'kondisi'                            => $this->whenLoaded('kondisi'),
            'jurusan'                            => $this->whenLoaded('jurusan'),
            'ruang'                              => $this->whenLoaded('ruang'),
            'lokasi'                             => $this->whenLoaded('lokasi'),
        ];
    }
}
