<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TahunAjaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_tahun_ajaran' => $this->id_tahun_ajaran,
            'tahun_ajaran'    => $this->tahun_ajaran,
            'semester'        => $this->semester,
            'tanggal_mulai'   => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'is_active'       => $this->is_active,
        ];
    }
}
