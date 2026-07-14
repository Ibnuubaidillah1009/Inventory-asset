<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKerusakanRequest;
use App\Http\Resources\KerusakanResource;
use App\Models\Aset;
use App\Models\Kerusakan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Kerusakan Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="PerbaikanItemResource",
 *     type="object",
 *     description="Representasi data item perbaikan di dalam kerusakan",
 *     @OA\Property(property="id_perbaikan", type="integer", example=1),
 *     @OA\Property(property="id_kerusakan", type="integer", example=1),
 *     @OA\Property(property="tanggal_perbaikan", type="string", format="date", nullable=true, example="2026-04-18"),
 *     @OA\Property(property="teknisi", type="string", nullable=true, example="Budi Santoso"),
 *     @OA\Property(property="biaya_perbaikan", type="number", nullable=true, example=250000),
 *     @OA\Property(property="tindakan_perbaikan", type="string", nullable=true, example="Ganti layar LCD")
 * )
 *
 * @OA\Schema(
 *     schema="KerusakanResource",
 *     type="object",
 *     description="Representasi data laporan kerusakan",
 *     @OA\Property(property="id_kerusakan", type="integer", example=1),
 *     @OA\Property(property="kode_inventaris", type="string", example="INV-2026-001"),
 *     @OA\Property(property="aset", ref="#/components/schemas/AsetResource", nullable=true),
 *     @OA\Property(property="tanggal_lapor", type="string", format="date", example="2026-04-10"),
 *     @OA\Property(property="id_pelapor", type="integer", example=1),
 *     @OA\Property(property="deskripsi_kerusakan", type="string", nullable=true, example="Layar retak"),
 *     @OA\Property(property="tingkat_kerusakan", type="string", example="Ringan"),
 *     @OA\Property(property="status_kerusakan", type="string", example="Menunggu Pemeriksaan"),
 *     @OA\Property(property="pelapor", ref="#/components/schemas/PenggunaResource", nullable=true),
 *     @OA\Property(property="perbaikan", type="array", @OA\Items(ref="#/components/schemas/PerbaikanItemResource"))
 * )
 *
 * @OA\Schema(
 *     schema="StoreKerusakanRequest",
 *     type="object",
 *     required={"kode_inventaris","tanggal_lapor","id_pelapor","deskripsi_kerusakan","tingkat_kerusakan"},
 *     description="Payload untuk membuat laporan kerusakan baru",
 *     @OA\Property(property="kode_inventaris", type="string", example="INV-2026-001"),
 *     @OA\Property(property="tanggal_lapor", type="string", format="date", example="2026-04-18"),
 *     @OA\Property(property="id_pelapor", type="integer", example=1),
 *     @OA\Property(property="deskripsi_kerusakan", type="string", example="Layar monitor berkedip-kedip"),
 *     @OA\Property(property="tingkat_kerusakan", type="string", enum={"Ringan","Sedang","Berat"}, example="Ringan"),
 *     @OA\Property(property="status_kerusakan", type="string", nullable=true, example="Menunggu Pemeriksaan")
 * )
 *
 * @OA\Schema(
 *     schema="KerusakanListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar kerusakan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar kerusakan berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/KerusakanResource"))
 * )
 *
 * @OA\Schema(
 *     schema="KerusakanSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu kerusakan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail kerusakan berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/KerusakanResource")
 * )
 *
 * @OA\Schema(
 *     schema="KerusakanDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan kerusakan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Data kerusakan berhasil dihapus.")
 * )
 */
class KerusakanController extends Controller
{
    /**
     * Tampilkan daftar semua laporan kerusakan.
     * Eager load: aset → masterBarang, pelapor, perbaikan
     *
     * @OA\Get(
     *     path="/kerusakan",
     *     operationId="indexKerusakan",
     *     tags={"Kerusakan"},
     *     summary="Daftar semua laporan kerusakan",
     *     description="Mengambil daftar semua laporan kerusakan aset beserta relasi aset, pelapor, dan perbaikan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar kerusakan berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KerusakanListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Kerusakan::with([
            'aset.masterBarang',
            'pelapor',
            'perbaikan',
        ])->orderByDesc('id_kerusakan')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar kerusakan berhasil diambil.',
            'data'    => KerusakanResource::collection($data),
        ]);
    }

    /**
     * Simpan laporan kerusakan baru menggunakan DB::transaction().
     *
     * Proses:
     * 1. Insert data ke tabel kerusakan.
     * 2. Update kondisi aset menjadi "Rusak Berat" dan status menjadi "Non-Aktif".
     *
     * Validasi dilakukan di StoreKerusakanRequest::withValidator().
     *
     * @OA\Post(
     *     path="/kerusakan",
     *     operationId="storeKerusakan",
     *     tags={"Kerusakan"},
     *     summary="Buat laporan kerusakan baru",
     *     description="Menyimpan laporan kerusakan aset. Kondisi aset otomatis diubah menjadi 'Rusak Berat' dan status 'Non-Aktif'. ID pelapor diambil dari user yang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreKerusakanRequest")
     *     ),
     *     @OA\Response(response=201, description="Laporan kerusakan berhasil disimpan",
     *         @OA\JsonContent(ref="#/components/schemas/KerusakanSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal"),
     *     @OA\Response(response=500, description="Gagal menyimpan laporan kerusakan")
     * )
     */
    public function store(StoreKerusakanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $kerusakan = DB::transaction(function () use ($validated, $request) {

                // ──────────────────────────────────────────────────────
                // 1. Insert data kerusakan
                // ──────────────────────────────────────────────────────
                $kerusakan = Kerusakan::create([
                    'kode_inventaris'     => $validated['kode_inventaris'],
                    'tanggal_lapor'       => $validated['tanggal_lapor'],
                    'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
                    'tingkat_kerusakan'   => $validated['tingkat_kerusakan'],
                    'id_pelapor'          => $request->user()->id_pengguna,
                    'status_kerusakan'    => 'Menunggu Pemeriksaan',
                ]);

                // ──────────────────────────────────────────────────────
                // 2. Update kondisi & status aset
                // ──────────────────────────────────────────────────────
                Aset::where('kode_inventaris', $validated['kode_inventaris'])->update([
                    'status' => 'Rusak Berat',
                ]);

                return $kerusakan;
            });

            // Eager load untuk response
            $kerusakan->load([
                'aset.masterBarang',
                'pelapor',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Laporan kerusakan berhasil disimpan.',
                'data'    => new KerusakanResource($kerusakan),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan laporan kerusakan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan detail satu laporan kerusakan.
     *
     * @OA\Get(
     *     path="/kerusakan/{id}",
     *     operationId="showKerusakan",
     *     tags={"Kerusakan"},
     *     summary="Detail laporan kerusakan",
     *     description="Mengambil detail satu laporan kerusakan beserta aset, pelapor, dan data perbaikan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kerusakan", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Detail kerusakan berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KerusakanSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kerusakan tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $kerusakan = Kerusakan::with([
            'aset.masterBarang',
            'pelapor',
            'perbaikan',
        ])->find($id);

        if (!$kerusakan) {
            return response()->json([
                'status'  => false,
                'message' => 'Kerusakan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail kerusakan berhasil diambil.',
            'data'    => new KerusakanResource($kerusakan),
        ]);
    }

    /**
     * Hapus data kerusakan.
     *
     * @OA\Delete(
     *     path="/kerusakan/{id}",
     *     operationId="destroyKerusakan",
     *     tags={"Kerusakan"},
     *     summary="Hapus laporan kerusakan",
     *     description="Menghapus data laporan kerusakan berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kerusakan", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Data kerusakan berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/KerusakanDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kerusakan tidak ditemukan"),
     *     @OA\Response(response=500, description="Gagal menghapus data kerusakan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $kerusakan = Kerusakan::find($id);

        if (!$kerusakan) {
            return response()->json([
                'status'  => false,
                'message' => 'Kerusakan tidak ditemukan.',
            ], 404);
        }

        try {
            $kerusakan->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data kerusakan berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus data kerusakan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
