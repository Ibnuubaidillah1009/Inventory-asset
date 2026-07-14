<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KerusakanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_kerusakan'        => $this->id_kerusakan,
            'kode_inventaris'     => $this->kode_inventaris,
            'tanggal_lapor'       => $this->tanggal_lapor ? $this->tanggal_lapor->format('Y-m-d') : null,
            'id_pelapor'          => $this->id_pelapor,
            'deskripsi_kerusakan' => $this->deskripsi_kerusakan,
            'tingkat_kerusakan'   => $this->tingkat_kerusakan,
            'status_kerusakan'    => $this->status_kerusakan,
            'aset'                => new AsetResource($this->whenLoaded('aset')),
            'pelapor'             => new PenggunaResource($this->whenLoaded('pelapor')),
            'perbaikan'           => PerbaikanResource::collection($this->whenLoaded('perbaikan')),
        ];
    }
}
