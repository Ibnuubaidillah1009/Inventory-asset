<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPermintaan extends Model
{
    protected $table = 'detail_permintaan';
    protected $primaryKey = 'id_detail_permintaan';
    public $timestamps = false;

    protected $fillable = [
        'kode_permintaan',
        'id_master_barang',
        'jumlah_diminta',
        'alasan_kebutuhan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_diminta' => 'integer',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: DetailPermintaan belongsTo Permintaan.
     */
    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'kode_permintaan', 'kode_permintaan');
    }

    /**
     * Relasi: DetailPermintaan belongsTo MasterBarang.
     */
    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'id_master_barang', 'id_master_barang');
    }
}
