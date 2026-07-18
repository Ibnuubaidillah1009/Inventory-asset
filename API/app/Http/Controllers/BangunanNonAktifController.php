<?php

namespace App\Http\Controllers;

use App\Models\BangunanNonAktif;
use App\Http\Requests\StoreBangunanNonAktifRequest;
use App\Http\Resources\BangunanNonAktifResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="BangunanNonAktifResource", type="object",
 *     description="Representasi data bangunan non-aktif",
 *     @OA\Property(property="id_nonaktif", type="integer", example=1),
 *     @OA\Property(property="kode_bangunan", type="integer", example=1),
 *     @OA\Property(property="id_status", type="integer", example=2),
 *     @OA\Property(property="tanggal", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Roboh"),
 *     @OA\Property(property="aset_bangunan", ref="#/components/schemas/AsetBangunanResource", nullable=true),
 *     @OA\Property(property="status_barang", ref="#/components/schemas/StatusBarangResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreBangunanNonAktifRequest", type="object",
 *     required={"kode_bangunan","id_status","tanggal"},
 *     @OA\Property(property="kode_bangunan", type="integer", example=1),
 *     @OA\Property(property="id_status", type="integer", example=2),
 *     @OA\Property(property="tanggal", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdateBangunanNonAktifRequest", type="object",
 *     @OA\Property(property="kode_bangunan", type="integer"),
 *     @OA\Property(property="id_status", type="integer"),
 *     @OA\Property(property="tanggal", type="string", format="date"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="BangunanNonAktifListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar bangunan non-aktif berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BangunanNonAktifResource"))
 * )
 * @OA\Schema(schema="BangunanNonAktifSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail bangunan non-aktif berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/BangunanNonAktifResource")
 * )
 * @OA\Schema(schema="BangunanNonAktifDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Bangunan non-aktif berhasil dihapus.")
 * )
 */
class BangunanNonAktifController extends Controller
{
    /**
     * @OA\Get(path="/bangunan-non-aktif", operationId="indexBangunanNonAktif", tags={"Bangunan Non-Aktif"}, summary="Daftar bangunan non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BangunanNonAktifListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = BangunanNonAktif::with(['asetBangunan', 'statusBarang'])->paginate(20);
        return BangunanNonAktifResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar bangunan non-aktif berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/bangunan-non-aktif", operationId="storeBangunanNonAktif", tags={"Bangunan Non-Aktif"}, summary="Tambah bangunan non-aktif", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreBangunanNonAktifRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/BangunanNonAktifSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreBangunanNonAktifRequest $request): JsonResponse
    {
        $item = BangunanNonAktif::create($request->validated());
        $item->load(['asetBangunan', 'statusBarang']);
        return response()->json(['status' => true, 'message' => 'Bangunan non-aktif berhasil ditambahkan.', 'data' => new BangunanNonAktifResource($item)], 201);
    }

    /**
     * @OA\Get(path="/bangunan-non-aktif/{id}", operationId="showBangunanNonAktif", tags={"Bangunan Non-Aktif"}, summary="Detail bangunan non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BangunanNonAktifSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $item = BangunanNonAktif::with(['asetBangunan', 'statusBarang'])->find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Bangunan non-aktif tidak ditemukan.'], 404);
        return response()->json(['status' => true, 'message' => 'Detail bangunan non-aktif berhasil diambil.', 'data' => new BangunanNonAktifResource($item)]);
    }

    /**
     * @OA\Put(path="/bangunan-non-aktif/{id}", operationId="updateBangunanNonAktif", tags={"Bangunan Non-Aktif"}, summary="Update bangunan non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateBangunanNonAktifRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BangunanNonAktifSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $item = BangunanNonAktif::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Bangunan non-aktif tidak ditemukan.'], 404);

        $validated = $request->validate([
            'kode_bangunan' => 'sometimes|integer|exists:aset_bangunan,kode_bangunan',
            'id_status'     => 'sometimes|integer|exists:status_barang,id_status',
            'tanggal'       => 'sometimes|date',
            'keterangan'    => 'nullable|string',
        ]);

        $item->update($validated);
        return response()->json(['status' => true, 'message' => 'Bangunan non-aktif berhasil diperbarui.', 'data' => new BangunanNonAktifResource($item->fresh(['asetBangunan', 'statusBarang']))]);
    }

    /**
     * @OA\Delete(path="/bangunan-non-aktif/{id}", operationId="destroyBangunanNonAktif", tags={"Bangunan Non-Aktif"}, summary="Hapus bangunan non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/BangunanNonAktifDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $item = BangunanNonAktif::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Bangunan non-aktif tidak ditemukan.'], 404);
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Bangunan non-aktif berhasil dihapus.']);
    }
}
