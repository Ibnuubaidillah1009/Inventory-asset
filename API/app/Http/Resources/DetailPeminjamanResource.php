<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_detail_pinjam' => $this->id_detail_pinjam,
            'nomor_peminjaman' => $this->nomor_peminjaman,
            'kode_barang'      => $this->kode_barang,
            'aset'             => new AsetResource($this->whenLoaded('aset')),
        ];
    }
}
