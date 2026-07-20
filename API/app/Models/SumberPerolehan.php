<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberPerolehan extends Model
{
    protected $table = 'sumber_perolehan';
    protected $primaryKey = 'id_sumber_perolehan';
    public $timestamps = false;

    protected $fillable = [
        'kode_sumber',
        'nama_sumber',
        'keterangan',
        'is_active',
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

    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'id_sumber_perolehan', 'id_sumber_perolehan');
    }
}
