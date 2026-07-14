<?php

namespace App\Http\Controllers;

use App\Models\TanahNonAktif;
use App\Http\Requests\StoreTanahNonAktifRequest;
use App\Http\Resources\TanahNonAktifResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="TanahNonAktifResource", type="object",
 *     description="Representasi data tanah non-aktif",
 *     @OA\Property(property="id_nonaktif", type="integer", example=1),
 *     @OA\Property(property="kode_tanah", type="integer", example=1),
 *     @OA\Property(property="id_status", type="integer", example=2),
 *     @OA\Property(property="tanggal", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Sengketa"),
 *     @OA\Property(property="aset_tanah", ref="#/components/schemas/AsetTanahResource", nullable=true),
 *     @OA\Property(property="status_barang", ref="#/components/schemas/StatusBarangResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreTanahNonAktifRequest", type="object",
 *     required={"kode_tanah","id_status","tanggal"},
 *     @OA\Property(property="kode_tanah", type="integer", example=1),
 *     @OA\Property(property="id_status", type="integer", example=2),
 *     @OA\Property(property="tanggal", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdateTanahNonAktifRequest", type="object",
 *     @OA\Property(property="kode_tanah", type="integer"),
 *     @OA\Property(property="id_status", type="integer"),
 *     @OA\Property(property="tanggal", type="string", format="date"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="TanahNonAktifListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar tanah non-aktif berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TanahNonAktifResource"))
 * )
 * @OA\Schema(schema="TanahNonAktifSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail tanah non-aktif berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/TanahNonAktifResource")
 * )
 * @OA\Schema(schema="TanahNonAktifDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Tanah non-aktif berhasil dihapus.")
 * )
 */
class TanahNonAktifController extends Controller
{
    /**
     * @OA\Get(path="/tanah-non-aktif", operationId="indexTanahNonAktif", tags={"Tanah Non-Aktif"}, summary="Daftar tanah non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/TanahNonAktifListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = TanahNonAktif::with(['asetTanah', 'statusBarang'])->get();
        return response()->json(['status' => true, 'message' => 'Daftar tanah non-aktif berhasil diambil.', 'data' => TanahNonAktifResource::collection($data)]);
    }

    /**
     * @OA\Post(path="/tanah-non-aktif", operationId="storeTanahNonAktif", tags={"Tanah Non-Aktif"}, summary="Tambah tanah non-aktif", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreTanahNonAktifRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/TanahNonAktifSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreTanahNonAktifRequest $request): JsonResponse
    {
        $item = TanahNonAktif::create($request->validated());
        $item->load(['asetTanah', 'statusBarang']);
        return response()->json(['status' => true, 'message' => 'Tanah non-aktif berhasil ditambahkan.', 'data' => new TanahNonAktifResource($item)], 201);
    }

    /**
     * @OA\Get(path="/tanah-non-aktif/{id}", operationId="showTanahNonAktif", tags={"Tanah Non-Aktif"}, summary="Detail tanah non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/TanahNonAktifSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $item = TanahNonAktif::with(['asetTanah', 'statusBarang'])->find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Tanah non-aktif tidak ditemukan.'], 404);
        return response()->json(['status' => true, 'message' => 'Detail tanah non-aktif berhasil diambil.', 'data' => new TanahNonAktifResource($item)]);
    }

    /**
     * @OA\Put(path="/tanah-non-aktif/{id}", operationId="updateTanahNonAktif", tags={"Tanah Non-Aktif"}, summary="Update tanah non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateTanahNonAktifRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/TanahNonAktifSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $item = TanahNonAktif::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Tanah non-aktif tidak ditemukan.'], 404);

        $validated = $request->validate([
            'kode_tanah'  => 'sometimes|integer|exists:aset_tanah,kode_tanah',
            'id_status'   => 'sometimes|integer|exists:status_barang,id_status',
            'tanggal'     => 'sometimes|date',
            'keterangan'  => 'nullable|string',
        ]);

        $item->update($validated);
        return response()->json(['status' => true, 'message' => 'Tanah non-aktif berhasil diperbarui.', 'data' => new TanahNonAktifResource($item->fresh(['asetTanah', 'statusBarang']))]);
    }

    /**
     * @OA\Delete(path="/tanah-non-aktif/{id}", operationId="destroyTanahNonAktif", tags={"Tanah Non-Aktif"}, summary="Hapus tanah non-aktif", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/TanahNonAktifDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $item = TanahNonAktif::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Tanah non-aktif tidak ditemukan.'], 404);
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Tanah non-aktif berhasil dihapus.']);
    }
}
