<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_peran'   => $this->id_peran,
            'nama_peran' => $this->nama_peran,
            'keterangan' => $this->keterangan,
            'akses_list' => AksesResource::collection($this->whenLoaded('aksesList')),
        ];
    }
}
