<?php

namespace App\Http\Controllers;

use App\Models\AsetHabisPakai;
use App\Http\Requests\StoreAsetHabisPakaiRequest;
use App\Http\Resources\AsetHabisPakaiResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="AsetHabisPakaiResource", type="object",
 *     description="Representasi data aset habis pakai",
 *     @OA\Property(property="kode_barang", type="string", example="HP-001"),
 *     @OA\Property(property="nama_barang", type="string", example="Kertas A4"),
 *     @OA\Property(property="id_satuan", type="integer", example=1),
 *     @OA\Property(property="stok_minimal", type="integer", example=10),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Kertas HVS"),
 *     @OA\Property(property="gambar", type="string", nullable=true),
 *     @OA\Property(property="satuan", ref="#/components/schemas/SatuanResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreAsetHabisPakaiRequest", type="object",
 *     required={"kode_barang","nama_barang"},
 *     @OA\Property(property="kode_barang", type="string", example="HP-001"),
 *     @OA\Property(property="nama_barang", type="string", example="Kertas A4"),
 *     @OA\Property(property="id_satuan", type="integer", nullable=true, example=1),
 *     @OA\Property(property="stok_minimal", type="integer", nullable=true, example=10),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="gambar", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdateAsetHabisPakaiRequest", type="object",
 *     @OA\Property(property="nama_barang", type="string", example="Updated"),
 *     @OA\Property(property="id_satuan", type="integer", nullable=true),
 *     @OA\Property(property="stok_minimal", type="integer", nullable=true),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="gambar", type="string", nullable=true)
 * )
 * @OA\Schema(schema="AsetHabisPakaiListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar aset habis pakai berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AsetHabisPakaiResource"))
 * )
 * @OA\Schema(schema="AsetHabisPakaiSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail aset habis pakai berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/AsetHabisPakaiResource")
 * )
 * @OA\Schema(schema="AsetHabisPakaiDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Aset habis pakai berhasil dihapus.")
 * )
 */
class AsetHabisPakaiController extends Controller
{
    /**
     * @OA\Get(path="/aset-habis-pakai", operationId="indexAsetHabisPakai", tags={"Aset Habis Pakai"}, summary="Daftar aset habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetHabisPakaiListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = AsetHabisPakai::with(['masterBarang.satuan', 'kondisi'])->paginate(20);
        return AsetHabisPakaiResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar aset habis pakai berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/aset-habis-pakai", operationId="storeAsetHabisPakai", tags={"Aset Habis Pakai"}, summary="Tambah aset habis pakai", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreAsetHabisPakaiRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/AsetHabisPakaiSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreAsetHabisPakaiRequest $request): JsonResponse
    {
        $item = AsetHabisPakai::create($request->validated());
        $item->load(['masterBarang.satuan', 'kondisi']);
        return response()->json(['status' => true, 'message' => 'Aset habis pakai berhasil ditambahkan.', 'data' => new AsetHabisPakaiResource($item)], 201);
    }

    /**
     * @OA\Get(path="/aset-habis-pakai/{id}", operationId="showAsetHabisPakai", tags={"Aset Habis Pakai"}, summary="Detail aset habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="HP-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetHabisPakaiSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $item = AsetHabisPakai::with(['masterBarang.satuan', 'kondisi'])->find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Aset habis pakai tidak ditemukan.'], 404);
        return response()->json(['status' => true, 'message' => 'Detail aset habis pakai berhasil diambil.', 'data' => new AsetHabisPakaiResource($item)]);
    }

    /**
     * @OA\Put(path="/aset-habis-pakai/{id}", operationId="updateAsetHabisPakai", tags={"Aset Habis Pakai"}, summary="Update aset habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="HP-001")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateAsetHabisPakaiRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetHabisPakaiSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $item = AsetHabisPakai::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Aset habis pakai tidak ditemukan.'], 404);

        $validated = $request->validate([
            'id_master_barang' => 'nullable|integer|exists:master_barang,id_master_barang',
            'stok'            => 'sometimes|integer|min:0',
            'id_kondisi'      => 'nullable|integer|exists:kondisi,id_kondisi',
            'status'          => 'nullable|string|max:50',
            'keterangan'      => 'nullable|string',
            'is_returnable'   => 'nullable|boolean',
        ]);

        $item->update($validated);
        return response()->json(['status' => true, 'message' => 'Aset habis pakai berhasil diperbarui.', 'data' => new AsetHabisPakaiResource($item->fresh(['masterBarang.satuan', 'kondisi']))]);
    }

    /**
     * @OA\Delete(path="/aset-habis-pakai/{id}", operationId="destroyAsetHabisPakai", tags={"Aset Habis Pakai"}, summary="Hapus aset habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="HP-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetHabisPakaiDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $item = AsetHabisPakai::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Aset habis pakai tidak ditemukan.'], 404);
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Aset habis pakai berhasil dihapus.']);
    }
}
