<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AksesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Support hierarchical menu tree dengan children rekursif.
     */
    public function toArray(Request $request): array
    {
        return [
            'id_akses'   => $this->id_akses,
            'id_parent'  => $this->id_parent,
            'nama_modul' => $this->nama_modul,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relasi parent (jika di-load)
            'parent'   => new AksesResource($this->whenLoaded('parent')),

            // Relasi children rekursif (jika di-load)
            'children' => AksesResource::collection($this->whenLoaded('childrenRecursive')),

            // Pivot permissions (jika dimuat dari relasi peran)
            'hak_baca'  => $this->whenPivotLoaded('peran_akses', fn() => (bool) $this->pivot->hak_baca),
            'hak_buat'  => $this->whenPivotLoaded('peran_akses', fn() => (bool) $this->pivot->hak_buat),
            'hak_ubah'  => $this->whenPivotLoaded('peran_akses', fn() => (bool) $this->pivot->hak_ubah),
            'hak_hapus' => $this->whenPivotLoaded('peran_akses', fn() => (bool) $this->pivot->hak_hapus),
        ];
    }
}
