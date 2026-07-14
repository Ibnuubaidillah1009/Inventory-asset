<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanHabisPakai extends Model
{
    protected $table = 'pengadaan_habis_pakai';
    protected $primaryKey = 'kode_inventaris';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_inventaris',
        'tanggal_pengadaan',
        'kode_barang',
        'jumlah',
        'harga_satuan',
        'id_pemasok',
        'kode_gudang',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengadaan' => 'date',
            'harga_satuan'      => 'decimal:2',
            'jumlah'            => 'integer',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    public function asetHabisPakai()
    {
        return $this->belongsTo(AsetHabisPakai::class, 'kode_barang', 'kode_barang');
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok', 'id_pemasok');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}
