<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerekResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_merek'   => $this->id_merek,
            'nama_merek' => $this->nama_merek,
            'keterangan' => $this->keterangan,
        ];
    }
}
