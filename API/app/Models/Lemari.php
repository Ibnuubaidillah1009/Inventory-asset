<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lemari extends Model
{
    protected $table = 'lemari';
    protected $primaryKey = 'id_lemari';
    public $timestamps = false;

    protected $fillable = [
        'kode_lemari',
        'nama',
        'id_ruang',
        'nomor_rak',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'id_ruang', 'id_ruang');
    }
}
