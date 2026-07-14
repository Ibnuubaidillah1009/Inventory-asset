<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kerusakan extends Model
{
    protected $table = 'kerusakan';
    protected $primaryKey = 'id_kerusakan';
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'tanggal_lapor',
        'id_pelapor',
        'deskripsi_kerusakan',
        'tingkat_kerusakan',
        'status_kerusakan',
    ];

    /**
     * Cast atribut.
     */
    protected function casts(): array
    {
        return [
            'tanggal_lapor' => 'date',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Kerusakan belongsTo Aset.
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_barang', 'kode_barang');
    }

    /**
     * Relasi: Kerusakan belongsTo Pengguna sebagai pelapor.
     */
    public function pelapor()
    {
        return $this->belongsTo(Pengguna::class, 'id_pelapor', 'id_pengguna');
    }

    /**
     * Relasi: Kerusakan hasMany Perbaikan.
     */
    public function perbaikan()
    {
        return $this->hasMany(Perbaikan::class, 'id_kerusakan', 'id_kerusakan');
    }
}
