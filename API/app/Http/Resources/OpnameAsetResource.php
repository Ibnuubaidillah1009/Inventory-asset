<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpnameAsetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_opname_aset'    => $this->id_opname_aset,
            'kode_inventaris'   => $this->kode_inventaris,
            'tanggal_opname'    => $this->tanggal_opname,
            'keterangan'        => $this->keterangan,
            'id_kondisi'        => $this->id_kondisi,
            'pengadaan'         => $this->whenLoaded('pengadaan'),
            'kondisi'           => $this->whenLoaded('kondisi'),
        ];
    }
}
