<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'nomor_peminjaman'   => $this->nomor_peminjaman,
            'tanggal_pinjam'     => $this->tanggal_pinjam ? $this->tanggal_pinjam->format('Y-m-d') : null,
            'nama_peminjam'      => $this->nama_peminjam,
            'nomor_telepon'      => $this->nomor_telepon,
            'lama_pinjam_hari'   => $this->lama_pinjam_hari,
            'keterangan'         => $this->keterangan,
            'status_peminjaman'  => $this->status_peminjaman,
            'detail_peminjaman'  => DetailPeminjamanResource::collection($this->whenLoaded('detailPeminjaman')),
        ];
    }
}
