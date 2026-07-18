<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsetHabisPakaiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_barang'         => $this->kode_barang,
            'nama_barang'         => $this->whenLoaded('masterBarang', fn() => $this->masterBarang->nama_barang),
            'id_master_barang'    => $this->id_master_barang,
            'stok'                => $this->stok,
            'id_kondisi'          => $this->id_kondisi,
            'nama_kondisi'        => $this->whenLoaded('kondisi', fn() => $this->kondisi->nama_kondisi),
            'status'              => $this->status,
            'tanggal_registrasi'  => $this->tanggal_registrasi,
            'keterangan'          => $this->keterangan,
            'is_returnable'       => $this->is_returnable,
            'satuan'              => $this->whenLoaded('masterBarang', function () {
                return $this->masterBarang->relationLoaded('satuan') ? $this->masterBarang->satuan : null;
            }),
        ];
    }
}
