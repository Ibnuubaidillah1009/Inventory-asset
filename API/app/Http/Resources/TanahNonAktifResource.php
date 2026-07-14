<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TanahNonAktifResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_tanah_non_aktif' => $this->id_tanah_non_aktif,
            'kode_tanah'     => $this->kode_tanah,
            'id_status'      => $this->id_status,
            'tanggal'        => $this->tanggal,
            'keterangan'     => $this->keterangan,
            'aset_tanah'     => $this->whenLoaded('asetTanah'),
            'status_barang'  => $this->whenLoaded('statusBarang'),
        ];
    }
}
