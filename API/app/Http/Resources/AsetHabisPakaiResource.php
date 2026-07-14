<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsetHabisPakaiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_aset_habis_pakai' => $this->id_aset_habis_pakai,
            'kode_barang'         => $this->kode_barang,
            'nama_barang'         => $this->nama_barang,
            'id_satuan'           => $this->id_satuan,
            'stok_minimal'        => $this->stok_minimal,
            'keterangan'          => $this->keterangan,
            'gambar'              => $this->gambar,
            'satuan'              => $this->whenLoaded('satuan'),
        ];
    }
}
