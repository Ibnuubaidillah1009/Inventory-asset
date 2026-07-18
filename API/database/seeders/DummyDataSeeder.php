<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding 3 data untuk setiap tabel...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ==========================================
        // 1. DATA MASTER REFERENSI UTAMA & LOKASI
        // ==========================================
        $jurusanList = [
            'RPL', 'TKJ', 'LISTRIK', 'ELEKTRO', 'BUSANA',
            'DKV', 'BP', 'MEKATRONIKA', 'OTOMOTIF',
        ];
        foreach ($jurusanList as $i => $nama) {
            DB::table('jurusan')->insertOrIgnore(['id_jurusan' => $i + 1, 'nama_jurusan' => $nama]);
        }

        for ($i = 1; $i <= 3; $i++) {
            DB::table('mapel')->insertOrIgnore(['id_mapel' => $i, 'nama_mapel' => "Mapel Dummy $i"]);
            DB::table('unit')->insertOrIgnore(['id_unit' => $i, 'nama_unit' => "Unit Dummy $i"]);
            DB::table('lokasi')->insertOrIgnore(['id_lokasi' => $i, 'nama_lokasi' => "Lokasi Dummy $i"]);
            DB::table('kategori')->insertOrIgnore(['id_kategori' => $i + 1, 'nama_kategori' => "Kategori Dummy $i"]); // +1 karena bisa jadi ada ID 1 dari seeder lain (atau user tes)
            DB::table('merek')->insertOrIgnore(['id_merek' => $i + 1, 'nama_merek' => "Merek Dummy $i"]);
            DB::table('satuan')->insertOrIgnore(['id_satuan' => $i + 1, 'nama_satuan' => "Satuan $i"]);
            DB::table('pemasok')->insertOrIgnore([
                'id_pemasok' => $i,
                'nama_pemasok' => "Pemasok Dummy $i",
                'nomor_telepon' => "0812345678" . $i,
                'alamat' => "Alamat Pemasok $i"
            ]);
            DB::table('pengaturan')->insertOrIgnore([
                'id_pengaturan' => $i + 1,
                'nama_instansi' => "Instansi Dummy $i",
                'alamat_instansi' => "Lokasi Dummy $i",
                'logo_instansi' => "Logo Dummy $i",
                'wallpaper_aplikasi' => "wallpaper Dummy $i",
                'telpon' => "Telpon Dummy $i",
                'website' => "Website Dummy $i",
                'email' => "Email Dummy $i",
                'kota' => "Kota Dummy $i",
                'kepala_sekolah' => "Kepala Sekolah Dummy $i",
                'NIP' => "3570943805",
                'bagian_invetaris' => "Inventaris Dummy $i",
            ]);
            DB::table('peran')->insertOrIgnore(['id_peran' => $i + 1, 'nama_peran' => "Peran Dummy $i"]);
        }

        // ==========================================
        // 2. DATA MASTER REFERENSI TURUNAN
        // ==========================================
        $ruangList = [
            'Perpustakaan','Tata Usaha','WK1','WK2','WK3','WK4',
            'Bendahara Barang','Gudang','Masjid','Kepala Bengkel',
            'Server','LSP','Keuangan',
        ];
        foreach ($ruangList as $i => $nama) {
            DB::table('ruang')->insertOrIgnore(['id_ruang' => $i + 1, 'nama_ruang' => $nama]);
        }

        for ($i = 1; $i <= 3; $i++) {
            $idJurusan = DB::table('jurusan')->inRandomOrder()->value('id_jurusan') ?? 1;
            DB::table('rombel')->insertOrIgnore(['id_rombel' => $i, 'id_jurusan' => $idJurusan, 'nama_rombel' => "Rombel Dummy $i"]);

            $idRombel = DB::table('rombel')->inRandomOrder()->value('id_rombel') ?? 1;
            DB::table('kelas')->insertOrIgnore(['id_kelas' => $i, 'id_rombel' => $idRombel, 'nama_kelas' => "Kelas $i", 'tahun_ajaran' => '2025/2026']);
        }

        // ==========================================
        // 3. PENGGUNA
        // ==========================================
        for ($i = 1; $i <= 3; $i++) {
            $idPeran = DB::table('peran')->inRandomOrder()->value('id_peran') ?? 1;
            $idKelas = DB::table('kelas')->inRandomOrder()->value('id_kelas');
            $idMapel = DB::table('mapel')->inRandomOrder()->value('id_mapel');
            $idUnit = DB::table('unit')->inRandomOrder()->value('id_unit');

            DB::table('pengguna')->insertOrIgnore([
                'id_pengguna' => $i + 1, // ID 1 biasanya admin bawaan
                'username' => "user_dummy_$i",
                'password' => Hash::make('password'),
                'id_peran' => $idPeran,
                'id_kelas' => $idKelas,
                'id_mapel' => $idMapel,
                'id_unit'  => $idUnit
            ]);
        }

        // ==========================================
        // 4. MASTER BARANG & ASET
        // ==========================================
        for ($i = 1; $i <= 3; $i++) {
            $idKategori = DB::table('kategori')->inRandomOrder()->value('id_kategori');
            $idMerek = DB::table('merek')->inRandomOrder()->value('id_merek');
            $idSatuan = DB::table('satuan')->inRandomOrder()->value('id_satuan');

            DB::table('master_barang')->insertOrIgnore([
                'id_master_barang' => $i + 1,
                'nama_barang' => "Barang Dummy $i",
                'id_kategori' => $idKategori,
                'id_merek' => $idMerek,
                'id_satuan' => $idSatuan,
                'jenis_barang' => 'Inventaris',
                'stok_minimal' => 5,
                'stok_aktual' => 10
            ]);

            $idMaster = DB::table('master_barang')->inRandomOrder()->value('id_master_barang');
            $idRuang = DB::table('ruang')->inRandomOrder()->value('id_ruang');

            DB::table('aset')->insertOrIgnore([
                'kode_barang' => "ASET-DUMMY-$i",
                'id_master_barang' => $idMaster,
                'id_ruang' => $idRuang,
                'tanggal_registrasi' => now()->subDays(rand(1, 30))->toDateString(),
                'kondisi_barang' => 'Baik',
                'nilai_residu' => rand(100000, 5000000),
                'status_ketersediaan' => 'Tersedia'
            ]);
        }

        // ==========================================
        // 5. ASET BANGUNAN & ASET TANAH
        // ==========================================
        for ($i = 1; $i <= 3; $i++) {
            $idLokasi = DB::table('lokasi')->inRandomOrder()->value('id_lokasi');

            DB::table('aset_tanah')->insertOrIgnore([
                'id_tanah' => $i,
                'nama_tanah' => "Tanah Dummy $i",
                'id_lokasi' => $idLokasi,
                'luas_tanah' => rand(100, 1000),
                'tahun_pengadaan' => rand(2010, 2025),
                'alamat_lokasi' => "Alamat Dummy Tanah $i",
                'nomor_sertifikat' => "SRT-TNH-$i",
                'status_hak' => 'Hak Milik',
                'nilai_aset' => rand(50000000, 100000000)
            ]);

            $idTanah = DB::table('aset_tanah')->inRandomOrder()->value('id_tanah');

            DB::table('aset_bangunan')->insertOrIgnore([
                'id_bangunan' => $i,
                'nama_bangunan' => "Bangunan Dummy $i",
                'id_tanah' => $idTanah,
                'luas_bangunan' => rand(50, 500),
                'tahun_bangun' => rand(2010, 2025),
                'kondisi_bangunan' => 'Baik',
                'konstruksi_bertingkat' => 'Tidak',
                'konstruksi_beton' => 'Ya',
                'nilai_aset' => rand(100000000, 500000000)
            ]);
        }

        // ==========================================
        // 6. TRANSAKSI (Permintaan, Pengadaan, Barang Keluar, Opname Stok)
        // ==========================================
        for ($i = 1; $i <= 3; $i++) {
            $idPengguna = DB::table('pengguna')->inRandomOrder()->value('id_pengguna');
            $idMaster = DB::table('master_barang')->inRandomOrder()->value('id_master_barang');
            $idPemasok = DB::table('pemasok')->inRandomOrder()->value('id_pemasok');

            $nomorPermintaan = "PRM-DUMMY-$i";
            DB::table('permintaan')->insertOrIgnore([
                'nomor_permintaan' => $nomorPermintaan,
                'tanggal_permintaan' => now()->subDays(rand(1, 10))->toDateString(),
                'id_pemohon' => $idPengguna,
                'keterangan_keperluan' => "Keperluan Dummy $i",
                'status_persetujuan' => 'Disetujui',
                'id_penyetuju' => 1
            ]);

            DB::table('detail_permintaan')->insertOrIgnore([
                'id_detail_permintaan' => $i,
                'nomor_permintaan' => $nomorPermintaan,
                'id_master_barang' => $idMaster,
                'jumlah_diminta' => rand(1, 10),
                'alasan_kebutuhan' => "Alasan Dummy $i"
            ]);

            $nomorPengadaan = "PGD-DUMMY-$i";
            DB::table('pengadaan')->insertOrIgnore([
                'nomor_pengadaan' => $nomorPengadaan,
                'tanggal_pengadaan' => now()->toDateString(),
                'nomor_permintaan' => $nomorPermintaan,
                'id_pemasok' => $idPemasok,
                'total_harga' => rand(50000, 500000),
                'keterangan' => "Keterangan Dummy Pengadaan $i"
            ]);

            DB::table('detail_pengadaan')->insertOrIgnore([
                'id_detail_pengadaan' => $i,
                'nomor_pengadaan' => $nomorPengadaan,
                'id_master_barang' => $idMaster,
                'jumlah_masuk' => rand(1, 10),
                'harga_satuan' => rand(10000, 50000)
            ]);

            DB::table('barang_keluar')->insertOrIgnore([
                'id_barang_keluar' => $i,
                'tanggal_keluar' => now()->toDateString(),
                'id_master_barang' => $idMaster,
                'jumlah_keluar' => rand(1, 5),
                'keterangan' => "Barang Keluar Dummy $i"
            ]);

            DB::table('opname_stok')->insertOrIgnore([
                'id_opname_stok' => $i,
                'id_master_barang' => $idMaster,
                'tanggal_opname' => now()->toDateString(),
                'stok_sistem' => 10,
                'stok_fisik' => 10,
                'selisih' => 0,
                'keterangan' => "Opname Stok Dummy $i",
                'id_pemeriksa' => $idPengguna
            ]);
        }

        // ==========================================
        // 7. TRANSAKSI ASET (Mutasi, Kerusakan, Peminjaman, dll)
        // ==========================================
        for ($i = 1; $i <= 3; $i++) {
            $idPengguna = DB::table('pengguna')->inRandomOrder()->value('id_pengguna');
            $kodeBarang = DB::table('aset')->inRandomOrder()->value('kode_barang') ?? "ASET-DUMMY-1";
            $idRuang = DB::table('ruang')->inRandomOrder()->value('id_ruang');

            DB::table('mutasi')->insertOrIgnore([
                'id_mutasi' => $i,
                'kode_barang' => $kodeBarang,
                'tanggal_mutasi' => now()->toDateString(),
                'id_ruang_asal' => $idRuang,
                'id_ruang_tujuan' => $idRuang, // Sama tidak masalah untuk dummy
                'alasan_mutasi' => "Alasan Mutasi Dummy $i",
                'id_penanggung_jawab' => $idPengguna
            ]);

            DB::table('kerusakan')->insertOrIgnore([
                'id_kerusakan' => $i,
                'kode_barang' => $kodeBarang,
                'tanggal_lapor' => now()->toDateString(),
                'id_pelapor' => $idPengguna,
                'deskripsi_kerusakan' => "Kerusakan Dummy $i",
                'tingkat_kerusakan' => 'Ringan',
                'status_kerusakan' => 'Menunggu Pemeriksaan'
            ]);

            DB::table('perbaikan')->insertOrIgnore([
                'id_perbaikan' => $i,
                'id_kerusakan' => $i, // Sesuai ID di atas
                'tanggal_perbaikan' => now()->toDateString(),
                'teknisi' => "Teknisi Dummy $i",
                'biaya_perbaikan' => rand(50000, 200000),
                'tindakan_perbaikan' => "Tindakan Dummy $i"
            ]);

            DB::table('penghapusan_aset')->insertOrIgnore([
                'id_penghapusan' => $i,
                'kode_barang' => $kodeBarang,
                'tanggal_hapus' => now()->toDateString(),
                'alasan_hapus' => "Penghapusan Dummy $i",
                'id_penyetuju' => 1
            ]);

            DB::table('opname_aset')->insertOrIgnore([
                'id_opname_aset' => $i,
                'kode_barang' => $kodeBarang,
                'tanggal_opname' => now()->toDateString(),
                'kondisi_ditemukan' => 'Baik',
                'keterangan' => "Opname Aset Dummy $i",
                'id_pemeriksa' => $idPengguna
            ]);

            $nomorPeminjaman = "PJM-DUMMY-$i";
            DB::table('peminjaman')->insertOrIgnore([
                'nomor_peminjaman' => $nomorPeminjaman,
                'tanggal_pinjam' => now()->subDays(10)->toDateString(),
                'id_peminjam' => $idPengguna,
                'nomor_telepon' => "0898877" . $i,
                'lama_pinjam_hari' => 14,
                'keterangan' => "Peminjaman Dummy $i",
                'status_peminjaman' => 'Dikembalikan'
            ]);

            DB::table('detail_peminjaman')->insertOrIgnore([
                'id_detail_pinjam' => $i,
                'nomor_peminjaman' => $nomorPeminjaman,
                'kode_barang' => $kodeBarang
            ]);

            DB::table('pengembalian')->insertOrIgnore([
                'id_pengembalian' => $i,
                'nomor_peminjaman' => $nomorPeminjaman,
                'tanggal_kembali' => now()->toDateString()
            ]);
        }

        // ==========================================
        // 8. PERMINTAAN BARANG
        // ==========================================
        $penggunaIds = DB::table('pengguna')->pluck('id_pengguna')->toArray();
        $masterBarangIds = DB::table('master_barang')->pluck('id_master_barang')->toArray();

        $permintaanData = [
            [
                'kode' => 'PRM-2026-001',
                'keterangan' => 'Pengadaan laptop untuk praktikum pemrograman web semester genap',
                'status' => 'disetujui',
                'barang' => [['id' => $masterBarangIds[0] ?? 1, 'jumlah' => 10, 'alasan' => 'Kurangnya laptop di lab komputer']],
            ],
            [
                'kode' => 'PRM-2026-002',
                'keterangan' => 'Pengadaan printer untuk keperluan administrasi',
                'status' => 'disetujui',
                'barang' => [['id' => $masterBarangIds[1] ?? 2, 'jumlah' => 3, 'alasan' => 'Printer lama sudah tidak berfungsi']],
            ],
            [
                'kode' => 'PRM-2026-003',
                'keterangan' => 'Pengadaan proyektor untuk ruang presentasi',
                'status' => 'diproses',
                'barang' => [['id' => $masterBarangIds[2] ?? 3, 'jumlah' => 2, 'alasan' => 'Proyektor existing sudah redup']],
            ],
            [
                'kode' => 'PRM-2026-004',
                'keterangan' => 'Pengadaan meja dan kursi untuk ruang guru baru',
                'status' => 'diproses',
                'barang' => [
                    ['id' => $masterBarangIds[3] ?? 4, 'jumlah' => 15, 'alasan' => 'Ruang guru baru membutuhkan furniture'],
                    ['id' => $masterBarangIds[4] ?? 5, 'jumlah' => 15, 'alasan' => 'Kursi belum tersedia di ruang baru'],
                ],
            ],
            [
                'kode' => 'PRM-2026-005',
                'keterangan' => 'Pengadaan alat laboratorium IPA',
                'status' => 'ditolak',
                'barang' => [['id' => $masterBarangIds[5] ?? 6, 'jumlah' => 20, 'alasan' => 'Alat lab sudah rusak parah']],
            ],
            [
                'kode' => 'PRM-2026-006',
                'keterangan' => 'Pengadaan speaker aktif untuk acara sekolah',
                'status' => 'disetujui',
                'barang' => [['id' => $masterBarangIds[6] ?? 7, 'jumlah' => 5, 'alasan' => 'Speaker existing mati total']],
            ],
            [
                'kode' => 'PRM-2026-007',
                'keterangan' => 'Pengadaan AC untuk ruang kelas lantai 3',
                'status' => 'diproses',
                'barang' => [['id' => $masterBarangIds[7] ?? 8, 'jumlah' => 4, 'alasan' => 'Suhu ruangan terlalu panas']],
            ],
            [
                'kode' => 'PRM-2026-008',
                'keterangan' => 'Pengadaan whiteboard untuk kelas 12',
                'status' => 'disetujui',
                'barang' => [['id' => $masterBarangIds[8] ?? 9, 'jumlah' => 6, 'alasan' => 'Whiteboard sudah pudar tulisannya']],
            ],
        ];

        foreach ($permintaanData as $idx => $p) {
            $idPengguna = $penggunaIds[array_rand($penggunaIds)];
            $tglSetuju = in_array($p['status'], ['disetujui', 'ditolak']) ? now()->subDays(rand(1, 5))->toDateString() : null;
            $idPenyetuju = $tglSetuju ? $penggunaIds[array_rand($penggunaIds)] : null;

            DB::table('permintaan')->insertOrIgnore([
                'kode_permintaan'      => $p['kode'],
                'id_jurusan'           => null,
                'id_pengguna'          => $idPengguna,
                'tanggal_permintaan'   => now()->subDays(rand(1, 14))->toDateString(),
                'keterangan_keperluan' => $p['keterangan'],
                'status_persetujuan'   => $p['status'],
                'tanggal_persetujuan'  => $tglSetuju,
                'id_penyetuju'         => $idPenyetuju,
                'alasan_disetujui'     => $p['status'] === 'disetujui' ? 'Kebutuhan sudah sesuai anggaran' : ($p['status'] === 'ditolak' ? 'Anggaran tidak mencukupi untuk tahun ini' : null),
            ]);

            foreach ($p['barang'] as $detailIdx => $b) {
                DB::table('detail_permintaan')->insertOrIgnore([
                    'id_detail_permintaan' => ($idx + 1) * 100 + $detailIdx + 1,
                    'kode_permintaan'      => $p['kode'],
                    'id_master_barang'     => $b['id'],
                    'jumlah_diminta'       => $b['jumlah'],
                    'alasan_kebutuhan'     => $b['alasan'],
                ]);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Proses seeding selesai!');
    }
}
