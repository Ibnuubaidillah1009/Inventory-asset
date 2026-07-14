<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Sistem Informasi Manajemen Inventaris Sekolah API",
 *     version="1.0.0",
 *     description="REST API untuk Sistem Informasi Manajemen Inventaris Sekolah. Mendukung modul Auth, Pengguna, Peran & Akses, Master Sekolah, Master Barang, Manajemen Aset, Transaksi Peminjaman & Permintaan, Pemeliharaan & Akhir Siklus Aset, serta Manajemen Database.",
 *     @OA\Contact(
 *         email="admin@sekolah.sch.id",
 *         name="Admin Sekolah"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum"
 * )
 *
 * @OA\Tag(name="Auth", description="Autentikasi pengguna (login, logout, profil)")
 * @OA\Tag(name="Pengguna", description="Manajemen data pengguna sistem")
 * @OA\Tag(name="Peran", description="Manajemen peran (role) dan hak akses")
 * @OA\Tag(name="Akses", description="Manajemen modul akses / permission")
 * @OA\Tag(name="Jurusan", description="Master data jurusan sekolah")
 * @OA\Tag(name="Rombel", description="Master data rombongan belajar")
 * @OA\Tag(name="Kelas", description="Master data kelas")
 * @OA\Tag(name="Mapel", description="Master data mata pelajaran")
 * @OA\Tag(name="Unit", description="Master data unit sekolah")
 * @OA\Tag(name="Kategori", description="Master data kategori barang")
 * @OA\Tag(name="Merek", description="Master data merek barang")
 * @OA\Tag(name="Satuan", description="Master data satuan barang")
 * @OA\Tag(name="Master Barang", description="Master data barang inventaris")
 * @OA\Tag(name="Lokasi", description="Manajemen lokasi / gedung")
 * @OA\Tag(name="Ruang", description="Manajemen ruang di dalam lokasi")
 * @OA\Tag(name="Aset", description="Manajemen aset inventaris sekolah")
 * @OA\Tag(name="Peminjaman", description="Transaksi peminjaman aset")
 * @OA\Tag(name="Permintaan", description="Transaksi permintaan barang")
 * @OA\Tag(name="Mutasi", description="Mutasi perpindahan aset antar ruang")
 * @OA\Tag(name="Kerusakan", description="Laporan kerusakan aset")
 * @OA\Tag(name="Perbaikan", description="Data perbaikan aset rusak")
 * @OA\Tag(name="Penghapusan Aset", description="Penghapusan aset dari inventaris")
 * @OA\Tag(name="Opname Aset", description="Manajemen opname aset")
 * @OA\Tag(name="Pemasok", description="Manajemen data pemasok / supplier")
 * @OA\Tag(name="Gudang", description="Manajemen data gudang penyimpanan")
 * @OA\Tag(name="Pengadaan", description="Manajemen data pengadaan aset")
 * @OA\Tag(name="Barang Non-Aktif", description="Pencatatan barang non-aktif")
 * @OA\Tag(name="Tanah Non-Aktif", description="Pencatatan tanah non-aktif")
 * @OA\Tag(name="Bangunan Non-Aktif", description="Pencatatan bangunan non-aktif")
 * @OA\Tag(name="Aset Habis Pakai", description="Manajemen aset habis pakai")
 * @OA\Tag(name="Pengadaan Habis Pakai", description="Pengadaan aset habis pakai")
 * @OA\Tag(name="Barang Keluar", description="Manajemen pengeluaran aset habis pakai")
 * @OA\Tag(name="Pengaturan", description="Pengaturan profil dan identitas lembaga")
 * @OA\Tag(name="Database", description="Manajemen database (backup, restore, reset, koneksi)")
 *
 * ============================================================
 *  REUSABLE SCHEMA DEFINITIONS – Shared Resources
 * ============================================================
 *
 * @OA\Schema(schema="AsetBangunanResource", type="object",
 *     description="Representasi data aset bangunan",
 *     @OA\Property(property="kode_bangunan", type="integer", example=1),
 *     @OA\Property(property="nama_bangunan", type="string", example="Gedung A"),
 *     @OA\Property(property="luas_bangunan", type="number", nullable=true),
 *     @OA\Property(property="kondisi_bangunan", type="string", nullable=true),
 *     @OA\Property(property="nilai_aset", type="number", nullable=true),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="tanggal_bangunan", type="string", format="date", nullable=true),
 *     @OA\Property(property="id_kondisi", type="integer", nullable=true),
 *     @OA\Property(property="ukuran_p", type="number", nullable=true),
 *     @OA\Property(property="ukuran_l", type="number", nullable=true),
 *     @OA\Property(property="konstruksi", type="string", nullable=true),
 *     @OA\Property(property="kondisi", ref="#/components/schemas/KondisiResource", nullable=true)
 * )
 *
 * @OA\Schema(schema="AsetTanahResource", type="object",
 *     description="Representasi data aset tanah",
 *     @OA\Property(property="kode_tanah", type="integer", example=1),
 *     @OA\Property(property="nama_pemilik", type="string", nullable=true),
 *     @OA\Property(property="id_lokasi", type="integer", nullable=true),
 *     @OA\Property(property="luas_tanah", type="number", nullable=true),
 *     @OA\Property(property="letak_tanah", type="string", nullable=true),
 *     @OA\Property(property="nomor_sertifikat", type="string", nullable=true),
 *     @OA\Property(property="status_hak", type="string", nullable=true),
 *     @OA\Property(property="nilai_aset", type="number", nullable=true),
 *     @OA\Property(property="penggunaan", type="string", nullable=true),
 *     @OA\Property(property="tanggal_perolehan", type="string", format="date", nullable=true),
 *     @OA\Property(property="sumber_perolehan", type="string", nullable=true),
 *     @OA\Property(property="lokasi", ref="#/components/schemas/LokasiResource", nullable=true)
 * )
 *
 * @OA\Schema(schema="DetailPengadaanResource", type="object",
 *     description="Representasi data detail pengadaan",
 *     @OA\Property(property="id_detail", type="integer", example=1),
 *     @OA\Property(property="kode_inventaris", type="string", example="INV-2026-001"),
 *     @OA\Property(property="id_master_barang", type="integer", example=1),
 *     @OA\Property(property="jumlah_masuk", type="integer", example=10),
 *     @OA\Property(property="master_barang", ref="#/components/schemas/MasterBarangResource", nullable=true)
 * )
 *
 * @OA\Schema(schema="DetailPermintaanResource", type="object",
 *     description="Representasi data detail permintaan",
 *     @OA\Property(property="id_detail_permintaan", type="integer", example=1),
 *     @OA\Property(property="kode_permintaan", type="string", example="PMT-2026-001"),
 *     @OA\Property(property="id_master_barang", type="integer", example=1),
 *     @OA\Property(property="jumlah_diminta", type="integer", example=5),
 *     @OA\Property(property="alasan_kebutuhan", type="string", nullable=true),
 *     @OA\Property(property="master_barang", ref="#/components/schemas/MasterBarangResource", nullable=true)
 * )
 *
 * @OA\Schema(schema="DetailPeminjamanResource", type="object",
 *     description="Representasi data detail peminjaman",
 *     @OA\Property(property="id_detail_pinjam", type="integer", example=1),
 *     @OA\Property(property="nomor_peminjaman", type="string", example="PJM-2026-001"),
 *     @OA\Property(property="kode_barang", type="string", example="BRG-001"),
 *     @OA\Property(property="aset", ref="#/components/schemas/AsetResource", nullable=true)
 * )
 *
 * @OA\Schema(schema="BarangKeluarPHPResource", type="object",
 *     description="Subset pengadaan habis pakai pada barang keluar",
 *     @OA\Property(property="kode_inventaris", type="string"),
 *     @OA\Property(property="kode_barang", type="string"),
 *     @OA\Property(property="nama_barang", type="string", nullable=true)
 * )
 *
 * @OA\Schema(schema="PenggunaMinimalResource", type="object",
 *     description="Subset data pengguna (id + username)",
 *     @OA\Property(property="id_pengguna", type="integer", example=1),
 *     @OA\Property(property="username", type="string", example="admin")
 * )
 */
class Controller
{
    //
}
