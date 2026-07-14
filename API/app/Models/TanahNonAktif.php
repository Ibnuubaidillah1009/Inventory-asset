<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TanahNonAktif extends Model
{
    protected $table = 'tanah_non_aktif';
    protected $primaryKey = 'id_tanah_non_aktif';
    public $timestamps = false;

    protected $fillable = [
        'kode_tanah',
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

    public function asetTanah()
    {
        return $this->belongsTo(AsetTanah::class, 'kode_tanah', 'kode_tanah');
    }

    public function statusBarang()
    {
        return $this->belongsTo(StatusBarang::class, 'id_status', 'id_status');
    }
}
