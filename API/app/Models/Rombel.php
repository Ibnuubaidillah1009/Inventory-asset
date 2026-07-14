<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    protected $table = 'rombel';
    protected $primaryKey = 'id_rombel';
    public $timestamps = false;

    protected $fillable = [
        'kode_rombel',
        'nama',
        'tingkat',
        'is_active',
        'id_jurusan',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Rombel belongsTo Jurusan.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    /**
     * Relasi: Rombel hasMany Kelas.
     */
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_rombel', 'id_rombel');
    }
}
