<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KelasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_kelas'   => $this->id_kelas,
            'nama_kelas' => $this->nama_kelas,
            'id_rombel'  => $this->id_rombel,
            'rombel'     => new RombelResource($this->whenLoaded('rombel')),
        ];
    }
}
