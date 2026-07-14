<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Akses extends Model
{
    /**
     * Nama tabel di database.
     */
    protected $table = 'akses';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_akses';

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'id_parent',
        'nama_modul',
    ];

    // =========================================================================
    // RELASI HIERARCHICAL (Parent-Child)
    // =========================================================================

    /**
     * Relasi: Akses belongsTo parent Akses.
     * Parent menu (grouping) memiliki id_parent = null.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Akses::class, 'id_parent', 'id_akses');
    }

    /**
     * Relasi: Akses hasMany children Akses (satu level).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Akses::class, 'id_parent', 'id_akses');
    }

    /**
     * Relasi rekursif: Akses hasMany children, yang juga memuat children-nya
     * (nested menu tak terbatas).
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    // =========================================================================
    // RELASI PERAN
    // =========================================================================

    /**
     * Relasi: Akses belongsToMany Peran (melalui tabel pivot peran_akses).
     * Pivot menyimpan hak_baca, hak_buat, hak_ubah, hak_hapus.
     */
    public function peranList(): BelongsToMany
    {
        return $this->belongsToMany(
            Peran::class,
            'peran_akses',
            'id_akses',
            'id_peran',
            'id_akses',
            'id_peran'
        )->withPivot(['hak_baca', 'hak_buat', 'hak_ubah', 'hak_hapus']);
    }

    /**
     * Relasi: PeranAkses records langsung (untuk query lebih spesifik).
     */
    public function peranAksesList(): HasMany
    {
        return $this->hasMany(PeranAkses::class, 'id_akses', 'id_akses');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: hanya parent (root) menu.
     */
    public function scopeParents($query)
    {
        return $query->whereNull('id_parent');
    }

    /**
     * Scope: hanya child menu (menghindari konflik nama dengan relasi children()).
     */
    public function scopeChildModules($query)
    {
        return $query->whereNotNull('id_parent');
    }

    // =========================================================================
    // HELPER STATIS
    // =========================================================================

    /**
     * Ambil hierarchical menu tree lengkap.
     * Mengembalikan koleksi parent → children (rekursif).
     */
    public static function getMenuTree()
    {
        return static::parents()
            ->with('childrenRecursive')
            ->orderBy('id_akses')
            ->get();
    }

    /**
     * Ambil hierarchical menu tree untuk peran tertentu.
     * Hanya menampilkan menu yang perannya punya akses (minimal hak_baca).
     */
    public static function getMenuTreeForPeran(int $idPeran)
    {
        // Ambil semua id_akses yang peran ini punya akses
        $aksesIds = PeranAkses::where('id_peran', $idPeran)
            ->where('hak_baca', 1)
            ->pluck('id_akses')
            ->toArray();

        // Ambil id parent dari child yang punya akses
        $parentIds = static::whereIn('id_akses', $aksesIds)
            ->whereNotNull('id_parent')
            ->pluck('id_parent')
            ->unique()
            ->toArray();

        // Gabungkan parent + child
        $allIds = array_unique(array_merge($aksesIds, $parentIds));

        return static::whereIn('id_akses', $allIds)
            ->whereNull('id_parent')
            ->with(['childrenRecursive' => function ($query) use ($allIds) {
                $query->whereIn('id_akses', $allIds);
            }])
            ->orderBy('id_akses')
            ->get();
    }
}
