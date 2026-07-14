<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeranAkses extends Model
{
    /**
     * Nama tabel di database.
     */
    protected $table = 'peran_akses';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_peran_akses';

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'id_peran',
        'id_akses',
        'hak_baca',
        'hak_buat',
        'hak_ubah',
        'hak_hapus',
    ];

    /**
     * Nonaktifkan timestamps.
     */
    public $timestamps = false;

    /**
     * Cast atribut.
     */
    protected function casts(): array
    {
        return [
            'hak_baca'  => 'boolean',
            'hak_buat'  => 'boolean',
            'hak_ubah'  => 'boolean',
            'hak_hapus' => 'boolean',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: PeranAkses belongsTo Peran.
     */
    public function peran(): BelongsTo
    {
        return $this->belongsTo(Peran::class, 'id_peran', 'id_peran');
    }

    /**
     * Relasi: PeranAkses belongsTo Akses.
     */
    public function akses(): BelongsTo
    {
        return $this->belongsTo(Akses::class, 'id_akses', 'id_akses');
    }
}
