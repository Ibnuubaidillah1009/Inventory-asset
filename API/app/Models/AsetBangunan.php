<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetBangunan extends Model
{
    protected $table = 'aset_bangunan';
    protected $primaryKey = 'kode_bangunan';
    public $timestamps = false;

    protected $fillable = [
        'nama_bangunan',
        'luas_bangunan',
        'kondisi_bangunan',
        'nilai_aset',
        'keterangan',
        'tanggal_bangunan',
        'id_kondisi',
        'ukuran_p',
        'ukuran_l',
        'konstruksi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bangunan' => 'date',
            'nilai_aset'       => 'decimal:2',
            'ukuran_p'         => 'decimal:2',
            'ukuran_l'         => 'decimal:2',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: AsetBangunan belongsTo Kondisi.
     */
    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi', 'id_kondisi');
    }
}
