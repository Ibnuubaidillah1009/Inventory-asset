<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuan';
    protected $primaryKey = 'id_satuan';
    public $timestamps = false;

    protected $fillable = [
        'kode_satuan',
        'nama_satuan',
        'keterangan',
    ];

    public function masterBarang()
    {
        return $this->hasMany(MasterBarang::class, 'id_satuan', 'id_satuan');
    }
}
