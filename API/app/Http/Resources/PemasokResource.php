<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PemasokResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pemasok'    => $this->id_pemasok,
            'nama_pemasok'  => $this->nama_pemasok,
            'nomor_telepon' => $this->nomor_telepon,
            'alamat'        => $this->alamat,
            'keterangan'    => $this->keterangan,
        ];
    }
}
