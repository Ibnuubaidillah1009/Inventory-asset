<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'kode_permintaan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_permintaan',
        'id_jurusan',
        'id_pengguna',
        'tanggal_permintaan',
        'keterangan_keperluan',
        'status_persetujuan',
        'tanggal_persetujuan',
        'id_penyetuju',
        'alasan_disetujui'
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function penyetuju()
    {
        return $this->belongsTo(Pengguna::class, 'id_penyetuju', 'id_pengguna');
    }

    public function pengadaan()
    {
        return $this->belongsToMany(Pengadaan::class, 'pengadaan_permintaan', 'kode_permintaan', 'id_pengadaan');
    }

    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaan::class, 'kode_permintaan', 'kode_permintaan');
    }
}
