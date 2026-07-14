<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterBarangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_master_barang' => $this->id_master_barang,
            'nama_barang'      => $this->nama_barang,
            'id_kategori'      => $this->id_kategori,
            'kategori'         => new KategoriResource($this->whenLoaded('kategori')),
            'id_merek'         => $this->id_merek,
            'merek'            => new MerekResource($this->whenLoaded('merek')),
            'id_satuan'        => $this->id_satuan,
            'satuan'           => new SatuanResource($this->whenLoaded('satuan')),
            'jenis_barang'     => $this->jenis_barang,
            'keterangan'       => $this->keterangan,
        ];
    }
}
