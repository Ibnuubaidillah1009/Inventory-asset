<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyProsesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('opname_aset')->truncate();
        DB::table('barang_non_aktif')->truncate();
        DB::table('mutasi')->truncate();

        $aset = DB::table('aset')
            ->join('master_barang', 'aset.id_master_barang', '=', 'master_barang.id_master_barang')
            ->select('aset.kode_inventaris', 'master_barang.nama_barang')
            ->where('aset.kode_inventaris', 'REGEXP', '^INV-[0-9]{4}-')
            ->limit(30)
            ->get();

        if ($aset->count() < 20) return;

        $kondisi = DB::table('kondisi')->pluck('id_kondisi')->toArray();
        $statuses = DB::table('status_barang')->pluck('id_status')->toArray();
        $jurusan = DB::table('jurusan')->where('id_jurusan', '>=', 4)->get();

        $kondisiBaik = 2;
        $kondisiRusak = 4;
        $kondisiBerat = 1;
        $statusNonAktif = 1;
        $statusDihapus = 4;

        // =============================================
        // OPNAME ASET (10 data)
        // =============================================
        $opnameItems = [
            ['inv' => 0, 'kondisi' => $kondisiBaik,  'tgl' => '2026-07-15', 'ket' => 'Meja siswa ditemukan di ruang PPLG dalam kondisi baik, permukaan rapi tanpa goresan. Cocok dengan data inventaris tahun 2007.'],
            ['inv' => 1, 'kondisi' => $kondisiBaik,  'tgl' => '2026-07-12', 'ket' => 'Meja siswa di ruang WK2 masih dalam kondisi layak pakai. Empat kaki meja dalam keadaan kokoh.'],
            ['inv' => 2, 'kondisi' => $kondisiRusak,  'tgl' => '2026-07-10', 'ket' => 'Meja siswa di ruang WK1 mengalami patah pada bagian kaki belakang kiri. Perlu pengelasan untuk perbaikan.'],
            ['inv' => 3, 'kondisi' => $kondisiBaik,  'tgl' => '2026-07-08', 'ket' => 'Ditemukan di gudang penyimpanan WK4. Kondisi masih bagus, permukaan cat masih utuh.'],
            ['inv' => 4, 'kondisi' => $kondisiRusak,  'tgl' => '2026-07-05', 'ket' => 'Laci meja siswa sudah tidak bisa ditutup karena rel laci aus. Bagian atas meja masih dalam kondisi baik.'],
            ['inv' => 19, 'kondisi' => $kondisiBaik, 'tgl' => '2026-07-03', 'ket' => 'PC Core i5 di ruang server PPLG berfungsi normal. Suhu processor dalam batas aman, fan berputar dengan baik.'],
            ['inv' => 20, 'kondisi' => $kondisiBerat, 'tgl' => '2026-07-01', 'ket' => 'Mini Thermal Printer di TU mengalami kerusakan pada thermal head. Tinta tidak keluar sama sekali meskipun sudah diganti.'],
            ['inv' => 21, 'kondisi' => $kondisiBaik, 'tgl' => '2026-06-28', 'ket' => 'Meja komputer di lab PPLG masih berfungsi baik. Kabel power sudah diganti tahun lalu.'],
            ['inv' => 25, 'kondisi' => $kondisiBaik, 'tgl' => '2026-06-25', 'ket' => 'Google Home Assistant di ruang TU masih menyala dan merespon perintah suara dengan normal.'],
            ['inv' => 26, 'kondisi' => $kondisiRusak, 'tgl' => '2026-06-22', 'ket' => 'Bardi IPCAM di koridor WK2 tidak bisa menyala. Indicator LED mati, kemungkinan power supply bermasalah.'],
        ];

        $opnameData = [];
        foreach ($opnameItems as $item) {
            $opnameData[] = [
                'kode_inventaris' => $aset[$item['inv']]->kode_inventaris,
                'tanggal_opname'  => $item['tgl'],
                'id_kondisi'      => $item['kondisi'],
                'keterangan'      => $item['ket'],
            ];
        }
        DB::table('opname_aset')->insert($opnameData);

        // =============================================
        // BARANG NON AKTIF (8 data)
        // =============================================
        $bnaItems = [
            ['inv' => 5,  'status' => $statusNonAktif, 'jml' => 1, 'tgl' => '2026-06-20', 'ket' => 'Meja siswa dengan nomor inventaris INV-2007-10-002-015 dinyatakan non-aktif. Empat kaki meja patah dan permukaan sudah tidak rata. Biaya perbaikan diperkirakan melebihi Rp500.000, tidak ekonomis untuk direnovasi.'],
            ['inv' => 6,  'status' => $statusDihapus,  'jml' => 1, 'tgl' => '2026-06-18', 'ket' => 'Meja siswa dihapus dari daftar inventaris karena sudah tidak dapat digunakan. Bagian laci hilang dan permukaan meja terlihat sudah sangat usang setelah 19 tahun penggunaan.'],
            ['inv' => 7,  'status' => $statusNonAktif, 'jml' => 2, 'tgl' => '2026-06-15', 'ket' => 'Dua unit meja siswa dari ruang WK1 yang mengalami kerusakan struktural. Meja pertama kaki depan patah, meja kedua permukaan melengkung akibat paparan air hujan dari atap bocor.'],
            ['inv' => 8,  'status' => $statusNonAktif, 'jml' => 1, 'tgl' => '2026-06-10', 'ket' => 'Meja siswa dari ruang KANTIN ditemukan dalam kondisi berkarat pada rangka besi. Bagian kayu sudah dimakan rayap dan mudah patah.'],
            ['inv' => 9,  'status' => $statusDihapus,  'jml' => 1, 'tgl' => '2026-06-05', 'ket' => 'Meja siswa yang sudah tidak layak pakai dihapus dari sistem. Meja ditemukan di gudang WK4 dengan kondisi separuh bagian sudah lapuk.'],
            ['inv' => 22, 'status' => $statusNonAktif, 'jml' => 1, 'tgl' => '2026-05-20', 'ket' => 'Mini Thermal Printer di ruang TU mengalami kerusakan fatal pada thermal head. Unit sudah berusia 8 tahun dan komponen pengganti sudah tidak tersedia di pasaran.'],
            ['inv' => 23, 'status' => $statusDihapus,  'jml' => 2, 'tgl' => '2026-05-15', 'ket' => 'Dua unit meja komputer dari lab PPLG yang kaki-kakinya sudah berkarat parah. Permukaan meja juga sudah terkelupas dan tidak bisa menampung beban monitor dengan aman.'],
            ['inv' => 24, 'status' => $statusNonAktif, 'jml' => 1, 'tgl' => '2026-05-10', 'ket' => 'Meja komputer di ruang Perpus dinyatakan non-aktif karena bagian laci sudah copot total dan rangka bawah meja goyang. Perlu penilaian ulang sebelum diputuskan untuk direnovasi atau dihapus.'],
        ];

        $bnaData = [];
        foreach ($bnaItems as $item) {
            $bnaData[] = [
                'kode_inventaris' => $aset[$item['inv']]->kode_inventaris,
                'id_status'       => $item['status'],
                'jumlah_nonaktif' => $item['jml'],
                'tanggal'         => $item['tgl'],
                'keterangan'      => $item['ket'],
            ];
        }
        DB::table('barang_non_aktif')->insert($bnaData);

        // =============================================
        // MUTASI BARANG (7 data)
        // =============================================
        $mutasiItems = [
            ['inv' => 10, 'asal' => 6, 'tujuan' => 8, 'tgl' => '2026-07-10', 'ket' => 'Meja siswa dipindahkan dari ruang PPLG ke WK2 karena penambahan jumlah siswa di kelas WK2. Meja dalam kondisi baik dan siap digunakan.'],
            ['inv' => 11, 'asal' => 8, 'tujuan' => 6, 'tgl' => '2026-07-05', 'ket' => 'Meja siswa dikembalikan dari WK2 ke PPLG karena sudah tidak dibutuhkan setelah pengurangan rombel di WK2.'],
            ['inv' => 12, 'asal' => 9, 'tujuan' => 4, 'tgl' => '2026-06-28', 'ket' => 'Pemindahan meja siswa dari TU ke WK1 untuk menggantikan meja yang rusak. Meja dalam kondisi masih layak pakai.'],
            ['inv' => 13, 'asal' => 4, 'tujuan' => 9, 'tgl' => '2026-06-20', 'ket' => 'Meja siswa dipindahkan dari WK1 ke TU untuk kebutuhan meja kerja staf tata usaha.'],
            ['inv' => 14, 'asal' => 5, 'tujuan' => 6, 'tgl' => '2026-06-15', 'ket' => 'Meja siswa dari Perpus dipindahkan ke PPLG karena Perpus mendapat bantuan meja baru dari Dinas Pendidikan.'],
            ['inv' => 15, 'asal' => 6, 'tujuan' => 5, 'tgl' => '2026-06-10', 'ket' => 'Meja siswa dari PPLG dipindah ke Perpus untuk melengkapi ruang baca yang kekurangan meja.'],
            ['inv' => 16, 'asal' => 8, 'tujuan' => 9, 'tgl' => '2026-06-05', 'ket' => 'Pemindahan meja siswa dari WK2 ke TU untuk kebutuhan pendaftaran siswa baru tahun ajaran 2026/2027.'],
        ];

        $mutasiData = [];
        foreach ($mutasiItems as $i => $item) {
            $mutasiData[] = [
                'id_mutasi'         => $i + 1,
                'id_jurusan_asal'   => $item['asal'],
                'id_jurusan_tujuan' => $item['tujuan'],
                'kode_inventaris'   => $aset[$item['inv']]->kode_inventaris,
                'tanggal_mutasi'    => $item['tgl'],
                'alasan_mutasi'     => $item['ket'],
            ];
        }
        DB::table('mutasi')->insert($mutasiData);
    }
}
