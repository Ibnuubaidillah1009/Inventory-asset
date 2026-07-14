<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LokasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_lokasi'   => $this->id_lokasi,
            'nama_lokasi' => $this->nama_lokasi,
            'alamat'      => $this->alamat,
            'keterangan'  => $this->keterangan,
        ];
    }
}
