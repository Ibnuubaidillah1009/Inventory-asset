<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPermintaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_detail_permintaan' => $this->id_detail_permintaan,
            'kode_permintaan'      => $this->kode_permintaan,
            'id_master_barang'     => $this->id_master_barang,
            'jumlah_diminta'       => $this->jumlah_diminta,
            'alasan_kebutuhan'     => $this->alasan_kebutuhan,
            'master_barang'        => new MasterBarangResource($this->whenLoaded('masterBarang')),
        ];
    }
}
