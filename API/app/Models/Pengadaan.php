<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primaryKey = 'id_pengadaan';
    public $timestamps = true;

    protected $fillable = [
        'id_pemasok',
        'nomor_po',
        'nomor_faktur',
        'tanggal_pengadaan',
        'total_harga',
        'persentase_ppn',
        'nominal_ppn',
        'grand_total',
        'keterangan',
        'kode_gudang',
        'id_kondisi',
        'jumlah_pengadaan',
        'id_sumber_perolehan',
        'status',
        'tanggal_pengiriman',
        'nomor_po_lampiran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengadaan'  => 'date',
            'total_harga'        => 'decimal:2',
            'persentase_ppn'     => 'decimal:2',
            'nominal_ppn'        => 'decimal:2',
            'grand_total'        => 'decimal:2',
            'jumlah_pengadaan'   => 'integer',
            'tanggal_pengiriman' => 'date',
        ];
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok', 'id_pemasok');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi', 'id_kondisi');
    }

    public function sumberPerolehan()
    {
        return $this->belongsTo(SumberPerolehan::class, 'id_sumber_perolehan', 'id_sumber_perolehan');
    }

    public function detailPengadaan()
    {
        return $this->hasMany(DetailPengadaan::class, 'id_pengadaan', 'id_pengadaan');
    }

    public function aset()
    {
        return $this->hasManyThrough(
            Aset::class,
            DetailPengadaan::class,
            'id_pengadaan',        // FK di detail_pengadaan → pengadaan
            'id_detail_pengadaan', // FK di aset → detail_pengadaan
            'id_pengadaan',        // PK di pengadaan
            'id_detail_pengadaan'  // PK di detail_pengadaan
        );
    }

    public function permintaan()
    {
        return $this->belongsToMany(Permintaan::class, 'pengadaan_permintaan', 'id_pengadaan', 'kode_permintaan');
    }
}
