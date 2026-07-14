<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Peran extends Model
{
    /**
     * Nama tabel di database.
     */
    protected $table = 'peran';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_peran';

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'nama_peran',
    ];

    /**
     * Nonaktifkan timestamps jika tabel tidak memiliki kolom created_at/updated_at.
     */
    public $timestamps = false;

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi: Peran hasMany Pengguna.
     */
    public function pengguna(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'id_peran', 'id_peran');
    }

    /**
     * Relasi: Peran belongsToMany Akses (melalui tabel pivot peran_akses).
     * Pivot menyimpan hak_baca, hak_buat, hak_ubah, hak_hapus.
     */
    public function aksesList(): BelongsToMany
    {
        return $this->belongsToMany(
            Akses::class,
            'peran_akses',
            'id_peran',
            'id_akses',
            'id_peran',
            'id_akses'
        )->withPivot(['hak_baca', 'hak_buat', 'hak_ubah', 'hak_hapus']);
    }

    /**
     * Relasi langsung ke peran_akses (untuk query detail).
     */
    public function peranAksesList(): HasMany
    {
        return $this->hasMany(PeranAkses::class, 'id_peran', 'id_peran');
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    /**
     * Cek apakah peran ini memiliki hak tertentu pada modul tertentu.
     *
     * @param  string  $namaModul  Nama modul (child module)
     * @param  string  $jenisHak   hak_baca | hak_buat | hak_ubah | hak_hapus
     * @return bool
     */
    public function punyaHakAkses(string $namaModul, string $jenisHak): bool
    {
        return $this->aksesList()
            ->where('nama_modul', $namaModul)
            ->wherePivot($jenisHak, 1)
            ->exists();
    }
}
