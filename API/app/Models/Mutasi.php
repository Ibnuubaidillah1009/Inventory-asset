<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $table = 'mutasi';
    protected $primaryKey = 'id_mutasi';
    public $timestamps = false;

    protected $fillable = [
        'id_mutasi',
        'id_jurusan_asal',
        'id_jurusan_tujuan',
        'tanggal_mutasi',
        'alasan_mutasi',
        'kode_inventaris',
        // 'jumlah_mutasi',
    ];

    /**
     * Cast atribut.
     */
    protected function casts(): array
    {
        return [
            'tanggal_mutasi' => 'date',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    public function jurusanAsal()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan_asal', 'id_jurusan');
    }

    public function jurusanTujuan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan_tujuan', 'id_jurusan');
    }

}
