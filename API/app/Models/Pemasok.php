<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    protected $table = 'pemasok';
    protected $primaryKey = 'id_pemasok';
    public $timestamps = false;

    protected $fillable = [
        'id_pemasok',
        'nama_pemasok',
        'nomor_telepon',
        'alamat',
        'keterangan',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Pemasok hasMany Pengadaan.
     */
    public function pengadaan()
    {
        return $this->hasMany(\App\Models\Pengadaan::class ?? Model::class, 'id_pemasok', 'id_pemasok');
    }
}
