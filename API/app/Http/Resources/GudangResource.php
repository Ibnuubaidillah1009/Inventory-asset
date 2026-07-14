<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GudangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_gudang'  => $this->kode_gudang,
            'nama_gudang'  => $this->nama_gudang,
            'keterangan'   => $this->keterangan,
        ];
    }
}
