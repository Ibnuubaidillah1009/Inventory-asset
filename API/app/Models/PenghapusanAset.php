<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenghapusanAset extends Model
{
    protected $table = 'penghapusan_aset';
    protected $primaryKey = 'id_penghapusan';
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'tanggal_hapus',
        'alasan_hapus',
        'id_penyetuju',
    ];

    /**
     * Cast atribut.
     */
    protected function casts(): array
    {
        return [
            'tanggal_hapus' => 'date',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: PenghapusanAset belongsTo Aset.
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_barang', 'kode_barang');
    }

    /**
     * Relasi: PenghapusanAset belongsTo Pengguna sebagai penyetuju.
     */
    public function penyetuju()
    {
        return $this->belongsTo(Pengguna::class, 'id_penyetuju', 'id_pengguna');
    }
}
