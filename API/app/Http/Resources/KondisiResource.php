<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KondisiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_kondisi'   => $this->id_kondisi,
            'nama_kondisi' => $this->nama_kondisi,
            'keterangan'   => $this->keterangan,
        ];
    }
}
