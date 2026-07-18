<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Http\Requests\StoreBarangKeluarRequest;
use App\Http\Resources\BarangKeluarResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="BarangKeluarResource", type="object",
 *     description="Representasi data barang keluar",
 *     @OA\Property(property="no_transaksi", type="integer", example=1),
 *     @OA\Property(property="tanggal_keluar", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="kode_inventaris", type="string", example="INVHP-001"),
 *     @OA\Property(property="jumlah_keluar", type="integer", example=5),
 *     @OA\Property(property="id_ruang", type="integer", example=1),
 *     @OA\Property(property="nama_penerima", type="integer", example=2),
 *     @OA\Property(property="petugas", type="integer", example=1),
 *     @OA\Property(property="kode_gudang", type="string", example="GDG-001"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Dipakai untuk ujian"),
 *     @OA\Property(property="pengadaan_habis_pakai", ref="#/components/schemas/BarangKeluarPHPResource", nullable=true),
 *     @OA\Property(property="ruang", ref="#/components/schemas/RuangResource", nullable=true),
 *     @OA\Property(property="penerima", ref="#/components/schemas/PenggunaMinimalResource", nullable=true),
 *     @OA\Property(property="petugas_user", ref="#/components/schemas/PenggunaMinimalResource", nullable=true),
 *     @OA\Property(property="gudang", ref="#/components/schemas/GudangResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreBarangKeluarRequest", type="object",
 *     required={"tanggal_keluar","kode_inventaris","jumlah_keluar","id_ruang","nama_penerima","kode_gudang"},
 *     @OA\Property(property="tanggal_keluar", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="kode_inventaris", type="string", example="INVHP-001"),
 *     @OA\Property(property="jumlah_keluar", type="integer", example=5),
 *     @OA\Property(property="id_ruang", type="integer", example=1),
 *     @OA\Property(property="nama_penerima", type="integer", example=2),
 *     @OA\Property(property="kode_gudang", type="string", example="GDG-001"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Dipakai untuk ujian")
 * )
 * @OA\Schema(schema="UpdateBarangKeluarRequest", type="object",
 *     @OA\Property(property="tanggal_keluar", type="string", format="date"),
 *     @OA\Property(property="kode_inventaris", type="string"),
 *     @OA\Property(property="jumlah_keluar", type="integer"),
 *     @OA\Property(property="id_ruang", type="integer"),
 *     @OA\Property(property="nama_penerima", type="integer"),
 *     @OA\Property(property="kode_gudang", type="string"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="BarangKeluarListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar barang keluar berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BarangKeluarResource"))
 * )
 * @OA\Schema(schema="BarangKeluarSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail barang keluar berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/BarangKeluarResource")
 * )
 * @OA\Schema(schema="BarangKeluarDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Barang keluar berhasil dihapus.")
 * )
 */
class BarangKeluarController extends Controller
{
    /**
     * @OA\Get(path="/barang-keluar", operationId="indexBarangKeluar", tags={"Barang Keluar"}, summary="Daftar barang keluar", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangKeluarListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = BarangKeluar::with([
            'pengadaanHabisPakai.asetHabisPakai',
            'ruang',
            'penerima',
            'petugasUser',
            'gudang'
        ])->paginate(20);

        return BarangKeluarResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar barang keluar berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/barang-keluar", operationId="storeBarangKeluar", tags={"Barang Keluar"}, summary="Simpan barang keluar baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreBarangKeluarRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/BarangKeluarSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreBarangKeluarRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Asumsi petugas diambil dari user yang login (jika menggunakan auth Sanctum)
        // Jika tidak, bisa diset manual/default. Misalnya:
        $validated['petugas'] = auth()->id() ?? 1;

        $barangKeluar = BarangKeluar::create($validated);

        $barangKeluar->load([
            'pengadaanHabisPakai.asetHabisPakai',
            'ruang',
            'penerima',
            'petugasUser',
            'gudang'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Barang keluar berhasil ditambahkan.',
            'data'    => new BarangKeluarResource($barangKeluar)
        ], 201);
    }

    /**
     * @OA\Get(path="/barang-keluar/{id}", operationId="showBarangKeluar", tags={"Barang Keluar"}, summary="Detail barang keluar", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangKeluarSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $barangKeluar = BarangKeluar::with([
            'pengadaanHabisPakai.asetHabisPakai',
            'ruang',
            'penerima',
            'petugasUser',
            'gudang'
        ])->find($id);

        if (!$barangKeluar) {
            return response()->json(['status' => false, 'message' => 'Barang keluar tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail barang keluar berhasil diambil.',
            'data'    => new BarangKeluarResource($barangKeluar)
        ]);
    }

    /**
     * @OA\Put(path="/barang-keluar/{id}", operationId="updateBarangKeluar", tags={"Barang Keluar"}, summary="Update barang keluar", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateBarangKeluarRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangKeluarSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $barangKeluar = BarangKeluar::find($id);

        if (!$barangKeluar) {
            return response()->json(['status' => false, 'message' => 'Barang keluar tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'tanggal_keluar'  => 'sometimes|date',
            'kode_inventaris' => 'sometimes|string|exists:pengadaan_habis_pakai,kode_inventaris',
            'jumlah_keluar'   => 'sometimes|integer|min:1',
            'id_ruang'        => 'sometimes|integer|exists:ruang,id_ruang',
            'nama_penerima'   => 'sometimes|integer|exists:pengguna,id_pengguna',
            'kode_gudang'     => 'sometimes|string|exists:gudang,kode_gudang',
            'keterangan'      => 'nullable|string',
        ]);

        $barangKeluar->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Barang keluar berhasil diperbarui.',
            'data'    => new BarangKeluarResource($barangKeluar->fresh([
                'pengadaanHabisPakai.asetHabisPakai',
                'ruang',
                'penerima',
                'petugasUser',
                'gudang'
            ]))
        ]);
    }

    /**
     * @OA\Delete(path="/barang-keluar/{id}", operationId="destroyBarangKeluar", tags={"Barang Keluar"}, summary="Hapus barang keluar", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangKeluarDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $barangKeluar = BarangKeluar::find($id);

        if (!$barangKeluar) {
            return response()->json(['status' => false, 'message' => 'Barang keluar tidak ditemukan.'], 404);
        }

        $barangKeluar->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Barang keluar berhasil dihapus.',
        ]);
    }
}
