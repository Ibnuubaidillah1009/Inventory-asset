<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SumberPerolehanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_sumber_perolehan' => $this->id_sumber_perolehan,
            'kode_sumber'         => $this->kode_sumber,
            'nama_sumber'          => $this->nama_sumber,
            'keterangan'          => $this->keterangan,
            'is_active'           => $this->is_active,
        ];
    }
}
