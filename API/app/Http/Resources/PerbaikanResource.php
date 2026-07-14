<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerbaikanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_perbaikan'        => $this->id_perbaikan,
            'id_kerusakan'        => $this->id_kerusakan,
            'tanggal_perbaikan'   => $this->tanggal_perbaikan ? $this->tanggal_perbaikan->format('Y-m-d') : null,
            'teknisi'             => $this->teknisi,
            'biaya_perbaikan'     => $this->biaya_perbaikan,
            'tindakan_perbaikan'  => $this->tindakan_perbaikan,
            'kerusakan'           => new KerusakanResource($this->whenLoaded('kerusakan')),
        ];
    }
}
