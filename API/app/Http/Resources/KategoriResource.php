<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_kategori'   => $this->id_kategori,
            'nama_kategori' => $this->nama_kategori,
            'keterangan'    => $this->keterangan,
        ];
    }
}
