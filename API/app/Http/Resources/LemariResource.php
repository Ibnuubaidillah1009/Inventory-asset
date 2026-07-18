<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LemariResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_lemari'  => $this->id_lemari,
            'kode_lemari' => $this->kode_lemari,
            'nama'        => $this->nama,
            'id_ruang'    => $this->id_ruang,
            'nomor_rak'   => $this->nomor_rak,
            'ruang'       => new RuangResource($this->whenLoaded('ruang')),
        ];
    }
}
