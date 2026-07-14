<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermintaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_permintaan'      => $this->kode_permintaan,
            'id_jurusan'           => $this->id_jurusan,
            'id_pengguna'          => $this->id_pengguna,
            'tahun_ajaran'         => $this->tahu_ajaran,
            'tanggal_permintaan'   => $this->tanggal_permintaan,
            'keterangan_keperluan' => $this->keterangan_keperluan,
            'status_persetujuan'   => $this->status_persetujuan,
            'tanggal_persetujuan'  => $this->tanggal_persetujuan,
            'id_penyetuju'         => $this->id_penyetuju,
            'alasan_disetujui'     => $this->alasan_disetujui,
            'jurusan'              => new JurusanResource($this->whenLoaded('jurusan')),
            'pengguna'             => new PenggunaResource($this->whenLoaded('pengguna')),
            'penyetuju'            => new PenggunaResource($this->whenLoaded('penyetuju')),
            'detail_permintaan'    => DetailPermintaanResource::collection($this->whenLoaded('detailPermintaan')),
        ];
    }
}
