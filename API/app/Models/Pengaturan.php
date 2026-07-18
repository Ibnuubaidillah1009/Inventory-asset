<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $primaryKey = 'id_pengaturan';

    public $timestamps = false;

    protected $fillable = [
        'nama_instansi',
        'alamat_instansi',
        'logo_instansi',
        'wallpaper_aplikasi',
        'telpon',
        'website',
        'email',
        'kota',
        'kepala_sekolah',
        'NIP',
        'bagian_inventaris',
        'kode_inventaris_prefix',
    ];
}
