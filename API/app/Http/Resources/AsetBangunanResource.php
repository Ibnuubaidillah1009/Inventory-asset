<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsetBangunanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_bangunan'     => $this->kode_bangunan,
            'nama_bangunan'     => $this->nama_bangunan,
            'luas_bangunan'     => $this->luas_bangunan,
            'kondisi_bangunan'  => $this->kondisi_bangunan,
            'nilai_aset'        => $this->nilai_aset,
            'keterangan'        => $this->keterangan,
            'tanggal_bangunan'  => $this->tanggal_bangunan,
            'id_kondisi'        => $this->id_kondisi,
            'ukuran_p'          => $this->ukuran_p,
            'ukuran_l'          => $this->ukuran_l,
            'konstruksi'        => $this->konstruksi,
            'kondisi'           => $this->whenLoaded('kondisi'),
        ];
    }
}
