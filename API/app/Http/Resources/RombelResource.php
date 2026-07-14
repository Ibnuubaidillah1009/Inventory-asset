<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RombelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_rombel'   => $this->id_rombel,
            'nama_rombel' => $this->nama_rombel,
            'id_jurusan'  => $this->id_jurusan,
            'jurusan'     => new JurusanResource($this->whenLoaded('jurusan')),
            'kelas'       => KelasResource::collection($this->whenLoaded('kelas')),
        ];
    }
}
