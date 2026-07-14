<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BangunanNonAktifResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_bangunan_non_aktif' => $this->id_bangunan_non_aktif,
            'kode_bangunan'   => $this->kode_bangunan,
            'id_status'       => $this->id_status,
            'tanggal'         => $this->tanggal,
            'keterangan'      => $this->keterangan,
            'aset_bangunan'   => $this->whenLoaded('asetBangunan'),
            'status_barang'   => $this->whenLoaded('statusBarang'),
        ];
    }
}
