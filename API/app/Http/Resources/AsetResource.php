<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_barang'        => $this->kode_barang,
            'id_master_barang'   => $this->id_master_barang,
            'tanggal_registrasi' => $this->tanggal_registrasi,
            'gambar'             => $this->gambar,
            'keterangan'         => $this->keterangan,
            'status'             => $this->status,
            'id_pengadaan'       => $this->id_pengadaan,
            'master_barang'      => clone $this->whenLoaded('masterBarang'),
            'pengadaan'          => $this->whenLoaded('pengadaan'),
        ];
    }
}
