<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusBarangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_status'  => $this->id_status,
            'nama_status' => $this->nama_status,
            'keterangan'  => $this->keterangan,
        ];
    }
}
