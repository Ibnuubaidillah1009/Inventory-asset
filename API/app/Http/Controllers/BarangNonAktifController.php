<?php

namespace App\Http\Controllers;

use App\Models\BarangNonAktif;
use App\Http\Requests\StoreBarangNonAktifRequest;
use App\Http\Resources\BarangNonAktifResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="BarangNonAktifResource", type="object",
 *     description="Representasi data barang non-aktif",
 *     @OA\Property(property="id_nonaktif", type="integer", example=1),
 *     @OA\Property(property="kode_inventaris", type="string", example="INV-2026-001"),
 *     @OA\Property(property="id_status", type="integer", example=2),
 *     @OA\Property(property="jumlah_nonaktif", type="integer", example=1),
 *     @OA\Property(property="tanggal", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Rusak"),
 *     @OA\Property(property="pengadaan", ref="#/components/schemas/PengadaanResource", nullable=true),
 *     @OA\Property(property="status_barang", ref="#/components/schemas/StatusBarangResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreBarangNonAktifRequest", type="object",
 *     required={"kode_inventaris","id_status","tanggal"},
 *     @OA\Property(property="kode_inventaris", type="string", example="INV-2026-001"),
 *     @OA\Property(property="id_status", type="integer", example=2),
 *     @OA\Property(property="jumlah_nonaktif", type="integer", example=1),
 *     @OA\Property(property="tanggal", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdateBarangNonAktifRequest", type="object",
 *     @OA\Property(property="kode_inventaris", type="string"),
 *     @OA\Property(property="id_status", type="integer"),
 *     @OA\Property(property="jumlah_nonaktif", type="integer"),
 *     @OA\Property(property="tanggal", type="string", format="date"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="BarangNonAktifListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar barang non-aktif berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BarangNonAktifResource"))
 * )
 * @OA\Schema(schema="BarangNonAktifSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail barang non-aktif berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/BarangNonAktifResource")
 * )
 * @OA\Schema(schema="BarangNonAktifDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Barang non-aktif berhasil dihapus.")
 * )
 */
class BarangNonAktifController extends Controller
{
    /**
     * @OA\Get(path="/barang-non-aktif", operationId="indexBarangNonAktif", tags={"Barang Non-Aktif"}, summary="Daftar barang non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangNonAktifListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = BarangNonAktif::with(['pengadaan.aset', 'statusBarang'])->get();
        return response()->json(['status' => true, 'message' => 'Daftar barang non-aktif berhasil diambil.', 'data' => BarangNonAktifResource::collection($data)]);
    }

    /**
     * @OA\Post(path="/barang-non-aktif", operationId="storeBarangNonAktif", tags={"Barang Non-Aktif"}, summary="Tambah barang non-aktif", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreBarangNonAktifRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/BarangNonAktifSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreBarangNonAktifRequest $request): JsonResponse
    {
        $item = BarangNonAktif::create($request->validated());
        $item->load(['pengadaan.aset', 'statusBarang']);
        return response()->json(['status' => true, 'message' => 'Barang non-aktif berhasil ditambahkan.', 'data' => new BarangNonAktifResource($item)], 201);
    }

    /**
     * @OA\Get(path="/barang-non-aktif/{id}", operationId="showBarangNonAktif", tags={"Barang Non-Aktif"}, summary="Detail barang non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangNonAktifSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $item = BarangNonAktif::with(['pengadaan.aset', 'statusBarang'])->find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Barang non-aktif tidak ditemukan.'], 404);
        return response()->json(['status' => true, 'message' => 'Detail barang non-aktif berhasil diambil.', 'data' => new BarangNonAktifResource($item)]);
    }

    /**
     * @OA\Put(path="/barang-non-aktif/{id}", operationId="updateBarangNonAktif", tags={"Barang Non-Aktif"}, summary="Update barang non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateBarangNonAktifRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangNonAktifSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $item = BarangNonAktif::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Barang non-aktif tidak ditemukan.'], 404);

        $validated = $request->validate([
            'kode_inventaris' => 'sometimes|string|max:50|exists:pengadaan,kode_inventaris',
            'id_status'       => 'sometimes|integer|exists:status_barang,id_status',
            'jumlah_nonaktif' => 'nullable|integer|min:1',
            'tanggal'         => 'sometimes|date',
            'keterangan'      => 'nullable|string',
        ]);

        $item->update($validated);
        return response()->json(['status' => true, 'message' => 'Barang non-aktif berhasil diperbarui.', 'data' => new BarangNonAktifResource($item->fresh(['pengadaan.aset', 'statusBarang']))]);
    }

    /**
     * @OA\Delete(path="/barang-non-aktif/{id}", operationId="destroyBarangNonAktif", tags={"Barang Non-Aktif"}, summary="Hapus barang non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BarangNonAktifDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $item = BarangNonAktif::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Barang non-aktif tidak ditemukan.'], 404);
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Barang non-aktif berhasil dihapus.']);
    }
}
