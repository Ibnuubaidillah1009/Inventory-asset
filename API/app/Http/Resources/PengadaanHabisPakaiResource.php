<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengadaanHabisPakaiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pengadaan_hp'   => $this->id_pengadaan_hp,
            'kode_inventaris'   => $this->kode_inventaris,
            'tanggal_pengadaan' => $this->tanggal_pengadaan,
            'kode_barang'       => $this->kode_barang,
            'jumlah'            => $this->jumlah,
            'harga_satuan'      => $this->harga_satuan,
            'id_pemasok'        => $this->id_pemasok,
            'kode_gudang'       => $this->kode_gudang,
            'keterangan'        => $this->keterangan,
            'aset_habis_pakai'  => $this->whenLoaded('asetHabisPakai'),
            'pemasok'           => $this->whenLoaded('pemasok'),
            'gudang'            => $this->whenLoaded('gudang'),
        ];
    }
}
