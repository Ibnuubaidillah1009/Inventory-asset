<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'nomor_peminjaman';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nomor_peminjaman',
        'tanggal_pinjam',
        'nama_peminjam',
        'nomor_telepon',
        'lama_pinjam_hari',
        'keterangan',
        'status_peminjaman',
    ];

    /**
     * Cast atribut.
     */
    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Peminjaman hasMany DetailPeminjaman.
     */
    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class, 'nomor_peminjaman', 'nomor_peminjaman');
    }
}
