<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';
    protected $primaryKey = 'no_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'tanggal_keluar',
        'kode_inventaris',
        'jumlah_keluar',
        'keterangan',
        'id_ruang',
        'nama_penerima',
        'petugas',
        'kode_gudang',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: BarangKeluar belongsTo PengadaanHabisPakai.
     */
    public function pengadaanHabisPakai()
    {
        return $this->belongsTo(PengadaanHabisPakai::class, 'kode_inventaris', 'kode_inventaris');
    }

    /**
     * Relasi: BarangKeluar belongsTo Ruang.
     */
    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'id_ruang', 'id_ruang');
    }

    /**
     * Relasi: BarangKeluar belongsTo Pengguna sebagai penerima.
     */
    public function penerima()
    {
        return $this->belongsTo(Pengguna::class, 'nama_penerima', 'id_pengguna');
    }

    /**
     * Relasi: BarangKeluar belongsTo Pengguna sebagai petugas.
     */
    public function petugasUser()
    {
        return $this->belongsTo(Pengguna::class, 'petugas', 'id_pengguna');
    }

    /**
     * Relasi: BarangKeluar belongsTo Gudang.
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}
