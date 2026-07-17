<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangNonAktifResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_barang_non_aktif' => $this->id_barang_non_aktif,
            'kode_inventaris' => $this->kode_inventaris,
            'id_status'       => $this->id_status,
            'jumlah_nonaktif' => $this->jumlah_nonaktif,
            'tanggal'         => $this->tanggal,
            'keterangan'      => $this->keterangan,
            'aset'           => $this->whenLoaded('aset'),
            'status_barang'   => $this->whenLoaded('statusBarang'),
        ];
    }
}
