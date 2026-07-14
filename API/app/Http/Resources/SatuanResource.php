<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SatuanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_satuan'   => $this->id_satuan,
            'nama_satuan' => $this->nama_satuan,
            'keterangan'  => $this->keterangan,
        ];
    }
}
