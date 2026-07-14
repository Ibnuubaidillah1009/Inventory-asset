<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merek extends Model
{
    protected $table = 'merek';
    protected $primaryKey = 'id_merek';
    public $timestamps = false;

    protected $fillable = [
        'nama_merek',
        'keterangan',
    ];

    public function masterBarang()
    {
        return $this->hasMany(MasterBarang::class, 'id_merek', 'id_merek');
    }
}
