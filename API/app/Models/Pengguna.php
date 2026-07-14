<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel di database.
     */
    protected $table = 'pengguna';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_pengguna';

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'username',
        'full_name',
        'password',
        'id_peran',
        'id_kelas',
        'id_mapel',
        'id_unit',
    ];

    /**
     * Kolom yang disembunyikan dari serialisasi JSON.
     */
    protected $hidden = [
        'password',
    ];

    public $timestamps = false;

    /**
     * Cast atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Pengguna belongsTo Peran.
     */
    public function peran()
    {
        return $this->belongsTo(Peran::class, 'id_peran', 'id_peran');
    }

    /**
     * Relasi: Pengguna belongsTo Kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    /**
     * Relasi: Pengguna belongsTo Mapel.
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    /**
     * Relasi: Pengguna belongsTo Unit.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }
}
