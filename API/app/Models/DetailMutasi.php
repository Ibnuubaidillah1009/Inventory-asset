<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailMutasi extends Model
{
    protected $table = 'detail_mutasi';
    protected $primaryKey = 'id_detail_mutasi'; // Asumsi ada primary key. Jika tidak, bisa diabaikan dengan null
    public $timestamps = false;

    protected $fillable = [
        'id_mutasi',
        'kode_inventaris',
    ];

    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class, 'id_mutasi', 'id_mutasi');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_inventaris', 'kode_inventaris');
    }
}
