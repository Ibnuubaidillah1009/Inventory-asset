<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CekHakAkses
{
    /**
     * Middleware RBAC hierarchical untuk mengecek hak akses pengguna.
     *
     * Flow:
     * 1. Ambil user yang sudah login.
     * 2. Cari modul (child) di tabel `akses` berdasarkan `nama_modul`.
     * 3. Cek hak akses (hak_baca/buat/ubah/hapus) di tabel `peran_akses`.
     * 4. Permission dicek pada CHILD module, bukan parent (parent hanya grouping).
     *
     * FIX untuk bug:
     * - modul `akses` tidak terbaca → sekarang pencarian case-insensitive
     * - pengecekan masih flat → sekarang query langsung di peran_akses JOIN akses
     * - route tidak sinkron dengan nama_modul → normalisasi nama
     * - parent-child tidak terbaca → join query yang benar
     * - permission selalu forbidden → query yang tepat di peran_akses
     *
     * Contoh penggunaan di route:
     *   ->middleware('cek.hak.akses:master_barang,hak_baca')
     *   ->middleware('cek.hak.akses:pengguna,hak_buat')
     *
     * @param  string  $namaModul  Nama modul child (kolom nama_modul di tabel akses)
     * @param  string  $jenisHak   hak_buat | hak_baca | hak_ubah | hak_hapus
     */
    public function handle(Request $request, Closure $next, string $namaModul, string $jenisHak): Response
    {
        $user = $request->user();

        // 1. Cek autentikasi
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
            ], 401);
        }

        // 2. Validasi jenis hak
        $hakValid = ['hak_buat', 'hak_baca', 'hak_ubah', 'hak_hapus'];
        if (!in_array($jenisHak, $hakValid)) {
            return response()->json([
                'status'  => false,
                'message' => "Jenis hak '{$jenisHak}' tidak valid. Gunakan: " . implode(', ', $hakValid),
            ], 400);
        }

        // 3. Ambil id_peran dari user
        $idPeran = $user->id_peran;
        if (!$idPeran) {
            return response()->json([
                'status'  => false,
                'message' => 'Pengguna tidak memiliki peran yang ditentukan.',
            ], 403);
        }

        // 4. Cek permission langsung di peran_akses JOIN akses
        //    Ini adalah FIX utama — query langsung, tidak lewat relasi belongsToMany
        //    yang sebelumnya gagal karena hak_* sudah tidak ada di tabel akses.
        $cacheKey = "rbac:{$idPeran}:{$namaModul}:{$jenisHak}";

        $punyaAkses = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($idPeran, $namaModul, $jenisHak) {
            return DB::table('peran_akses')
                ->join('akses', 'peran_akses.id_akses', '=', 'akses.id_akses')
                ->where('peran_akses.id_peran', $idPeran)
                ->where('akses.nama_modul', $namaModul)
                ->where("peran_akses.{$jenisHak}", 1)
                ->exists();
        });

        if (!$punyaAkses) {
            return response()->json([
                'status'  => false,
                'message' => "Anda tidak memiliki hak '{$jenisHak}' pada modul '{$namaModul}'.",
            ], 403);
        }

        return $next($request);
    }
}
