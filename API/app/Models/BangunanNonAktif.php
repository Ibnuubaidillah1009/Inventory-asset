<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BangunanNonAktif extends Model
{
    protected $table = 'bangunan_non_aktif';
    protected $primaryKey = 'id_bangunan_non_aktif';
    public $timestamps = false;

    protected $fillable = [
        'kode_bangunan',
        'id_status',
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

    public function asetBangunan()
    {
        return $this->belongsTo(AsetBangunan::class, 'kode_bangunan', 'kode_bangunan');
    }

    public function statusBarang()
    {
        return $this->belongsTo(StatusBarang::class, 'id_status', 'id_status');
    }
}
