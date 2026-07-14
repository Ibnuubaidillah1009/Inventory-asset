<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengaturanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pengaturan'      => $this->id_pengaturan,
            'nama_instansi'      => $this->nama_instansi,
            'alamat_instansi'    => $this->alamat_instansi,
            'logo_instansi'      => $this->logo_instansi,
            'wallpaper_aplikasi' => $this->wallpaper_aplikasi,
            'telpon'             => $this->telpon,
            'website'            => $this->website,
            'email'              => $this->email,
            'kota'               => $this->kota,
            'kepala_sekolah'     => $this->kepala_sekolah,
            'NIP'                => $this->NIP,
            'bagian_inventaris'  => $this->bagian_inventaris,
        ];
    }
}
