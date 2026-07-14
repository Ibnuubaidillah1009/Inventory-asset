<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PengadaanPermintaan extends Pivot
{
    protected $table = 'pengadaan_permintaan';
    public $timestamps = false;
    
    protected $fillable = [
        'id_pengadaan',
        'kode_permintaan',
    ];
}
