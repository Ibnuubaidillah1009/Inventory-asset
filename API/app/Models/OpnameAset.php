<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpnameAset extends Model
{
    protected $table = 'opname_aset';
    protected $primaryKey = 'id_opname_aset';

    protected $fillable = [
        'kode_inventaris',
        'tanggal_opname',
        'id_kondisi',
        'keterangan',
    ];

    public $timestamps = false;

    // =========================================================================
    // RELASI
    // =========================================================================

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_inventaris', 'kode_inventaris');
    }

    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi', 'id_kondisi');
    }
}
