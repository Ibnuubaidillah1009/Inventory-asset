<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun_ajaran';
    public $timestamps = false;

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'is_active',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'tanggal_mulai'    => 'date',
            'tanggal_selesai'  => 'date',
        ];
    }
}
