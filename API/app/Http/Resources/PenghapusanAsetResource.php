<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenghapusanAsetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_penghapusan'  => $this->id_penghapusan,
            'kode_barang'     => $this->kode_barang,
            'tanggal_hapus'   => $this->tanggal_hapus ? $this->tanggal_hapus->format('Y-m-d') : null,
            'alasan_hapus'    => $this->alasan_hapus,
            'id_penyetuju'    => $this->id_penyetuju,
            'aset'            => new AsetResource($this->whenLoaded('aset')),
            'penyetuju'       => new PenggunaResource($this->whenLoaded('penyetuju')),
        ];
    }
}
