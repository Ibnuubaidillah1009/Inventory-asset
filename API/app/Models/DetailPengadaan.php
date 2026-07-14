<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengadaan extends Model
{
    protected $table = 'detail_pengadaan';
    protected $primaryKey = 'id_detail_pengadaan';
    public $timestamps = false;

    protected $fillable = [
        'id_pengadaan',
        'id_master_barang',
        'jumlah_masuk',
        'harga_satuan',
        'pemasok',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_masuk' => 'integer',
            'harga_satuan' => 'decimal:2',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class, 'id_pengadaan', 'id_pengadaan');
    }

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'id_master_barang', 'id_master_barang');
    }
}
