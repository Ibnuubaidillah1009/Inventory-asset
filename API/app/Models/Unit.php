<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'unit';
    protected $primaryKey = 'id_unit';
    public $timestamps = false;

    protected $fillable = [
        'nama_unit',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Unit hasMany Pengguna.
     */
    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_unit', 'id_unit');
    }
}
