<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetTanah extends Model
{
    protected $table = 'aset_tanah';
    protected $primaryKey = 'kode_tanah';
    public $timestamps = false;

    protected $fillable = [
        'nama_pemilik',
        'id_lokasi',
        'luas_tanah',
        'letak_tanah',
        'nomor_sertifikat',
        'status_hak',
        'nilai_aset',
        'penggunaan',
        'tanggal_perolehan',
        'sumber_perolehan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perolehan' => 'date',
            'nilai_aset'        => 'decimal:2',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: AsetTanah belongsTo Lokasi.
     */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi', 'id_lokasi');
    }
}
