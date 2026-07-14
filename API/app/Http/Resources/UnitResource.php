<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_unit'   => $this->id_unit,
            'nama_unit' => $this->nama_unit,
        ];
    }
}
