<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapel';
    protected $primaryKey = 'id_mapel';
    public $timestamps = false;

    protected $fillable = [
        'nama_mapel',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Mapel hasMany Pengguna.
     */
    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_mapel', 'id_mapel');
    }
}
