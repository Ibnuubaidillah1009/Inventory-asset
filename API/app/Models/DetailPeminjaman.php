<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    protected $table = 'detail_peminjaman';
    protected $primaryKey = 'id_detail_pinjam';
    public $timestamps = false;

    protected $fillable = [
        'nomor_peminjaman',
        'kode_barang',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: DetailPeminjaman belongsTo Peminjaman.
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'nomor_peminjaman', 'nomor_peminjaman');
    }

    /**
     * Relasi: DetailPeminjaman belongsTo Aset.
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_barang', 'kode_barang');
    }
}
