<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primaryKey = 'id_pengadaan';
    public $timestamps = false;

    protected $fillable = [
        'tanggal_pengadaan',
        'total_harga',
        'keterangan',
        'kode_gudang',
        'jumlah_pengadaan',
        'id_sumber_perolehan',
        'status', // diproses, dibelanjakan, selesai
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengadaan' => 'date',
            'total_harga'       => 'decimal:2',
            'jumlah_pengadaan'  => 'integer',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    public function sumberPerolehan()
    {
        return $this->belongsTo(SumberPerolehan::class, 'id_sumber_perolehan', 'id_sumber_perolehan');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    public function detailPengadaan()
    {
        return $this->hasMany(DetailPengadaan::class, 'id_pengadaan', 'id_pengadaan');
    }

    public function aset()
    {
        return $this->hasMany(Aset::class, 'id_pengadaan', 'id_pengadaan');
    }

    public function permintaan()
    {
        return $this->belongsToMany(Permintaan::class, 'pengadaan_permintaan', 'id_pengadaan', 'kode_permintaan');
    }
}
