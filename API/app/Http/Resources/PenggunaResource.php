<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenggunaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_pengguna' => $this->id_pengguna,
            'username'    => $this->username,
            'full_name'   => $this->full_name,
            'id_peran'    => $this->id_peran,
            'peran'       => new PeranResource($this->whenLoaded('peran')),
            'id_kelas'    => $this->id_kelas,
            'kelas'       => new KelasResource($this->whenLoaded('kelas')),
            'id_mapel'    => $this->id_mapel,
            'mapel'       => new MapelResource($this->whenLoaded('mapel')),
            'id_unit'     => $this->id_unit,
            'unit'        => new UnitResource($this->whenLoaded('unit')),
        ];
    }
}
