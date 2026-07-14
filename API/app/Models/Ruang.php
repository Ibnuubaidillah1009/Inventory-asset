<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    protected $table = 'ruang';
    protected $primaryKey = 'id_ruang';
    public $timestamps = false;

    protected $fillable = [
        'kode_ruang',
        'id_lokasi',
        'nama_ruang',
        'lantai',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi', 'id_lokasi');
    }

    public function lemari()
    {
        return $this->hasMany(Lemari::class, 'id_ruang', 'id_ruang');
    }
}
