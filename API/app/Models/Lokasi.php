<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';
    protected $primaryKey = 'id_lokasi';
    public $timestamps = false;

    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
        'alamat',
        'keterangan',
    ];

    public function ruang()
    {
        return $this->hasMany(Ruang::class, 'id_lokasi', 'id_lokasi');
    }
}
