<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusBarang extends Model
{
    protected $table = 'status_barang';
    protected $primaryKey = 'id_status';
    public $timestamps = false;

    protected $fillable = [
        'nama_status',
        'keterangan',
    ];
}
