<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetHabisPakai extends Model
{
    protected $table = 'aset_habis_pakai';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'id_master_barang',
        'tanggal_registrasi',
        'gambar',
        'keterangan',
        'status',
        'id_pengadaan',
        'id_kondisi',
        'is_returnable',
    ];

    protected function casts(): array
    {
        return [
            'is_returnable' => 'boolean',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'id_master_barang', 'id_master_barang');
    }

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class, 'id_pengadaan', 'id_pengadaan');
    }

    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi', 'id_kondisi');
    }

    public function pengadaanHabisPakai()
    {
        return $this->hasMany(PengadaanHabisPakai::class, 'kode_barang', 'kode_barang');
    }
}
