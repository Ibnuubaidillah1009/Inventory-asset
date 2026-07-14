<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AksesSeeder extends Seeder
{
    /**
     * Seeder untuk tabel `akses` dengan struktur hierarchical parent-child.
     *
     * Aturan:
     * - Parent menu (id_parent = null) hanya sebagai grouping/kategori.
     * - Child menu adalah modul sebenarnya yang dicek permission-nya.
     * - Support nested menu tak terbatas via id_parent.
     * - Menggunakan updateOrInsert agar idempotent (aman dijalankan berulang).
     * - Tidak hardcode id_akses, referensi via nama_modul.
     */
    public function run(): void
    {
        $this->command->info('🔄 Membuat struktur hierarchical akses...');

        // =================================================================
        // DEFINISI HIERARCHICAL MENU
        // =================================================================
        // Format: 'nama_modul_parent' => ['child_1', 'child_2', ...]
        // Parent yang punya child nested: gunakan array asosiatif.
        //
        // Struktur ini support nested tak terbatas. Jika child punya sub-child,
        // gunakan format: 'child_name' => ['sub_child_1', 'sub_child_2']

        $menuTree = [
            'AUTENTIKASI' => [
                'login',
                'logout',
                'me',
            ],

            'MANAJEMEN PENGGUNA' => [
                'pengguna',
                'peran',
                'akses',
            ],

            'MASTER SEKOLAH' => [
                'jurusan',
                'rombel',
                'kelas',
                'mapel',
                'unit',
                'tahun_ajaran',
            ],

            'MASTER BARANG' => [
                'kategori',
                'merek',
                'satuan',
                'master_barang',
                'pemasok',
                'gudang',
                'sumber_perolehan',
            ],

            'MANAJEMEN ASET' => [
                'aset',
                'lokasi',
                'ruang',
                'lemari',
                'kondisi',
                'status_barang',
                'opname_aset',
                'pengadaan',
            ],

            'TRANSAKSI' => [
                'peminjaman',
                'permintaan',
                'mutasi',
                'barang_keluar',
                'kerusakan',
                'perbaikan',
            ],

            'ASET NON-AKTIF' => [
                'penghapusan_aset',
                'barang_non_aktif',
                'tanah_non_aktif',
                'bangunan_non_aktif',
            ],

            'HABIS PAKAI' => [
                'aset_habis_pakai',
                'pengadaan_habis_pakai',
            ],

            'PENGATURAN SISTEM' => [
                'pengaturan',
                'database',
            ],

            'LAPORAN' => [
                'laporan_barang',
                'laporan_transaksi',
                'laporan_peminjaman',
            ],
        ];

        // =================================================================
        // PROSES INSERT RECURSIVE
        // =================================================================
        $this->seedMenuTree($menuTree, null);

        $totalParent = DB::table('akses')->whereNull('id_parent')->count();
        $totalChild  = DB::table('akses')->whereNotNull('id_parent')->count();

        $this->command->info("✅ Seeder akses selesai: {$totalParent} parent, {$totalChild} child.");
    }

    /**
     * Recursive seeder: insert parent & child menu ke tabel akses.
     * Support nested tak terbatas.
     *
     * @param  array    $tree        Array tree menu
     * @param  int|null $parentId    ID parent (null = root)
     */
    private function seedMenuTree(array $tree, ?int $parentId): void
    {
        foreach ($tree as $key => $value) {
            if (is_array($value)) {
                // $key = nama parent, $value = array children
                $parentModulId = $this->upsertAkses($key, $parentId);

                // Rekursif untuk children
                $this->seedMenuTree($value, $parentModulId);
            } else {
                // $value = nama child (leaf module)
                $this->upsertAkses($value, $parentId);
            }
        }
    }

    /**
     * Insert atau update record akses berdasarkan nama_modul.
     * Mengembalikan id_akses dari record yang di-insert/update.
     *
     * @param  string   $namaModul  Nama modul
     * @param  int|null $parentId   ID parent
     * @return int                  id_akses
     */
    private function upsertAkses(string $namaModul, ?int $parentId): int
    {
        DB::table('akses')->updateOrInsert(
            ['nama_modul' => $namaModul],
            [
                'id_parent'  => $parentId,
            ]
        );

        $record = DB::table('akses')->where('nama_modul', $namaModul)->first();

        return $record->id_akses;
    }
}