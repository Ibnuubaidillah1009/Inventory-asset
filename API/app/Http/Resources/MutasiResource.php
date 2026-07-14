<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MutasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_mutasi'         => $this->id_mutasi,
            'id_jurusan_asal'   => $this->id_jurusan_asal,
            'id_jurusan_tujuan' => $this->id_jurusan_tujuan,
            'tanggal_mutasi'    => $this->tanggal_mutasi ? $this->tanggal_mutasi->format('Y-m-d') : null,
            // 'jumlah_mutasi'     => $this->jumlah_mutasi,
            'kode_inventaris'   => $this->kode_inventaris,
            'alasan_mutasi'     => $this->alasan_mutasi,
            'jurusan_asal'      => $this->whenLoaded('jurusanAsal'),
            'jurusan_tujuan'    => $this->whenLoaded('jurusanTujuan'),
        ];
    }
}
