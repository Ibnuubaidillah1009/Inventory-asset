<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsetTanahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_tanah'         => $this->kode_tanah,
            'nama_pemilik'       => $this->nama_pemilik,
            'id_lokasi'          => $this->id_lokasi,
            'luas_tanah'         => $this->luas_tanah,
            'letak_tanah'        => $this->letak_tanah,
            'nomor_sertifikat'   => $this->nomor_sertifikat,
            'status_hak'         => $this->status_hak,
            'nilai_aset'         => $this->nilai_aset,
            'penggunaan'         => $this->penggunaan,
            'tanggal_perolehan'  => $this->tanggal_perolehan,
            'sumber_perolehan'   => $this->sumber_perolehan,
            'lokasi'             => $this->whenLoaded('lokasi'),
        ];
    }
}
