<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeranAksesSeeder extends Seeder
{
    /**
     * Seeder untuk tabel `peran_akses` — mengatur hak akses per peran.
     *
     * Role default:
     * 1. Admin         → Full CRUD semua modul
     * 2. Guru          → Baca master tertentu, tidak boleh hapus
     * 3. Staff         → CRUD transaksi, baca master
     * 4. Kepala Sekolah → Hanya baca laporan dan dashboard
     *
     * Menggunakan nama_modul sebagai referensi (bukan hardcode id_akses).
     * Menggunakan updateOrInsert agar idempotent.
     */
    public function run(): void
    {
        $this->command->info('🔄 Membuat hak akses per peran...');

        // =================================================================
        // 1. PASTIKAN SEMUA PERAN ADA
        // =================================================================
        $peranList = ['Admin', 'Guru', 'Staff', 'Kepala Sekolah'];

        foreach ($peranList as $namaperan) {
            DB::table('peran')->updateOrInsert(
                ['nama_peran' => $namaperan],
                ['nama_peran' => $namaperan]
            );
        }

        // =================================================================
        // 2. AMBIL ID PERAN
        // =================================================================
        $idPeran = [];
        foreach ($peranList as $nama) {
            $peran = DB::table('peran')->where('nama_peran', $nama)->first();
            $idPeran[$nama] = $peran->id_peran;
        }

        // =================================================================
        // 3. AMBIL SEMUA CHILD MODULES (hanya child yang punya id_parent)
        //    Parent menu hanya grouping, tidak perlu permission check.
        // =================================================================
        $allChildModules = DB::table('akses')
            ->whereNotNull('id_parent')
            ->pluck('id_akses', 'nama_modul')
            ->toArray();

        // =================================================================
        // 4. DEFINISI HAK AKSES PER PERAN
        // =================================================================

        // --- ADMIN: Full CRUD semua modul ---
        $this->assignFullAccess($idPeran['Admin'], $allChildModules);

        // --- GURU: Baca master tertentu, tidak boleh hapus ---
        $guruModules = [
            // Autentikasi (baca saja)
            'login'    => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'logout'   => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'me'       => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Master Sekolah (baca saja)
            'jurusan'  => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'rombel'   => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'kelas'    => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'mapel'    => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'unit'     => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Master Barang (baca saja)
            'kategori'      => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'merek'         => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'satuan'        => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'master_barang' => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Peminjaman (bisa buat & baca, tidak hapus)
            'peminjaman' => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'permintaan' => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Kerusakan (bisa lapor, tidak hapus)
            'kerusakan' => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
        ];
        $this->assignModuleAccess($idPeran['Guru'], $guruModules, $allChildModules);

        // --- STAFF: CRUD transaksi, baca master ---
        $staffModules = [
            // Autentikasi
            'login'    => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'logout'   => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'me'       => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Master Sekolah (baca saja)
            'jurusan'  => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'rombel'   => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'kelas'    => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'mapel'    => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'unit'     => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Master Barang (baca + buat + ubah, tidak hapus)
            'kategori'      => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'merek'         => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'satuan'        => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'master_barang' => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'pemasok'       => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'gudang'        => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],

            // Manajemen Aset (CRUD)
            'aset'          => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],
            'lokasi'        => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'ruang'         => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'kondisi'       => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'status_barang' => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'opname_aset'   => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'pengadaan'     => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],

            // Transaksi (full CRUD)
            'peminjaman'    => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],
            'permintaan'    => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],
            'mutasi'        => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],
            'barang_keluar' => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],
            'kerusakan'     => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'perbaikan'     => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],

            // Aset Non-Aktif
            'penghapusan_aset'  => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'barang_non_aktif'  => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'tanah_non_aktif'   => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],
            'bangunan_non_aktif'=> ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 0],

            // Habis Pakai
            'aset_habis_pakai'      => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],
            'pengadaan_habis_pakai' => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 1, 'hak_hapus' => 1],

            // Laporan (baca saja)
            'laporan_barang'     => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'laporan_transaksi'  => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'laporan_peminjaman' => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
        ];
        $this->assignModuleAccess($idPeran['Staff'], $staffModules, $allChildModules);

        // --- KEPALA SEKOLAH: Hanya baca laporan dan dashboard ---
        $kepsekModules = [
            // Autentikasi
            'login'    => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'logout'   => ['hak_baca' => 1, 'hak_buat' => 1, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'me'       => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Master Sekolah (baca saja)
            'jurusan'  => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'rombel'   => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'kelas'    => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'mapel'    => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'unit'     => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Aset (baca saja)
            'aset'     => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'pengadaan'=> ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Permintaan (bisa approve/reject)
            'permintaan' => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 1, 'hak_hapus' => 0],

            // Pengaturan (baca saja)
            'pengaturan' => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],

            // Laporan (baca saja — ini yang utama)
            'laporan_barang'     => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'laporan_transaksi'  => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
            'laporan_peminjaman' => ['hak_baca' => 1, 'hak_buat' => 0, 'hak_ubah' => 0, 'hak_hapus' => 0],
        ];
        $this->assignModuleAccess($idPeran['Kepala Sekolah'], $kepsekModules, $allChildModules);

        $this->command->info('✅ Seeder peran_akses selesai untuk semua peran.');
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Assign full CRUD access ke semua child modules untuk peran tertentu.
     *
     * @param int   $idPeran        ID peran
     * @param array $allModules     ['nama_modul' => id_akses, ...]
     */
    private function assignFullAccess(int $idPeran, array $allModules): void
    {
        foreach ($allModules as $namaModul => $idAkses) {
            DB::table('peran_akses')->updateOrInsert(
                [
                    'id_peran' => $idPeran,
                    'id_akses' => $idAkses,
                ],
                [
                    'hak_baca'  => 1,
                    'hak_buat'  => 1,
                    'hak_ubah'  => 1,
                    'hak_hapus' => 1,
                ]
            );
        }
    }

    /**
     * Assign hak akses spesifik per modul untuk peran tertentu.
     *
     * @param int   $idPeran            ID peran
     * @param array $modulPermissions   ['nama_modul' => ['hak_baca'=>1, ...], ...]
     * @param array $allModules         ['nama_modul' => id_akses, ...]
     */
    private function assignModuleAccess(int $idPeran, array $modulPermissions, array $allModules): void
    {
        foreach ($modulPermissions as $namaModul => $hak) {
            // Skip jika modul tidak ada di database
            if (!isset($allModules[$namaModul])) {
                $this->command->warn("⚠️  Modul '{$namaModul}' tidak ditemukan di tabel akses, dilewati.");
                continue;
            }

            $idAkses = $allModules[$namaModul];

            DB::table('peran_akses')->updateOrInsert(
                [
                    'id_peran' => $idPeran,
                    'id_akses' => $idAkses,
                ],
                [
                    'hak_baca'  => $hak['hak_baca']  ?? 0,
                    'hak_buat'  => $hak['hak_buat']  ?? 0,
                    'hak_ubah'  => $hak['hak_ubah']  ?? 0,
                    'hak_hapus' => $hak['hak_hapus'] ?? 0,
                ]
            );
        }
    }
}
