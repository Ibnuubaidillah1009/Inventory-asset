<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangNonAktif extends Model
{
    protected $table = 'barang_non_aktif';
    protected $primaryKey = 'id_barang_non_aktif';
    public $timestamps = false;

    protected $fillable = [
        'kode_inventaris',
        'id_status',
        'jumlah_nonaktif',
        'tanggal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class, 'kode_inventaris', 'kode_inventaris');
    }

    public function statusBarang()
    {
        return $this->belongsTo(StatusBarang::class, 'id_status', 'id_status');
    }
}
