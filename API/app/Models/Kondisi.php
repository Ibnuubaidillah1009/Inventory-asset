<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kondisi extends Model
{
    protected $table = 'kondisi';
    protected $primaryKey = 'id_kondisi';
    public $timestamps = false;

    protected $fillable = [
        'nama_kondisi',
        'keterangan',
    ];
}
