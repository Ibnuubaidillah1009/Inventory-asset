<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'keterangan',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Kategori hasMany MasterBarang.
     */
    public function masterBarang()
    {
        return $this->hasMany(MasterBarang::class, 'id_kategori', 'id_kategori');
    }
}
