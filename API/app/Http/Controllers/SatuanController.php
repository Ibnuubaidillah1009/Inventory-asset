<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSatuanRequest;
use App\Http\Requests\UpdateSatuanRequest;
use App\Http\Resources\SatuanResource;
use App\Models\Satuan;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="SatuanResource", type="object",
 *     @OA\Property(property="id_satuan", type="integer", example=1),
 *     @OA\Property(property="nama_satuan", type="string", example="Unit"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Satuan barang")
 * )
 * @OA\Schema(schema="StoreSatuanRequest", type="object", required={"nama_satuan"},
 *     @OA\Property(property="nama_satuan", type="string", example="Unit"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Satuan barang")
 * )
 * @OA\Schema(schema="UpdateSatuanRequest", type="object",
 *     @OA\Property(property="nama_satuan", type="string", example="Unit Updated"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Updated info")
 * )
 * @OA\Schema(schema="SatuanListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar satuan berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SatuanResource"))
 * )
 * @OA\Schema(schema="SatuanSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail satuan berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/SatuanResource")
 * )
 * @OA\Schema(schema="SatuanDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Satuan berhasil dihapus.")
 * )
 */
class SatuanController extends Controller
{
    /**
     * Tampilkan daftar all satuan.
     *
     * @OA\Get(path="/satuan", operationId="indexSatuan", tags={"Satuan"}, summary="Daftar satuan", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/SatuanListResponse"))
     * )
     */
    public function index(): JsonResponse
    {
        $satuan = Satuan::all();
        return response()->json([
            'status'  => true,
            'message' => 'Daftar satuan berhasil diambil.',
            'data'    => SatuanResource::collection($satuan),
        ]);
    }

    /**
     * Simpan satuan baru.
     *
     * @OA\Post(path="/satuan", operationId="storeSatuan", tags={"Satuan"}, summary="Tambah satuan", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreSatuanRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/SatuanSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreSatuanRequest $request): JsonResponse
    {
        $satuan = Satuan::create($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil ditambahkan.',
            'data'    => new SatuanResource($satuan),
        ], 201);
    }

    /**
     * Tampilkan detail satuan.
     *
     * @OA\Get(path="/satuan/{id}", operationId="showSatuan", tags={"Satuan"}, summary="Detail satuan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/SatuanSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $satuan = Satuan::find($id);
        if (!$satuan) {
            return response()->json(['status' => false, 'message' => 'Satuan tidak ditemukan.'], 404);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Detail satuan berhasil diambil.',
            'data'    => new SatuanResource($satuan),
        ]);
    }

    /**
     * Update satuan.
     *
     * @OA\Put(path="/satuan/{id}", operationId="updateSatuan", tags={"Satuan"}, summary="Update satuan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateSatuanRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/SatuanSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(UpdateSatuanRequest $request, int $id): JsonResponse
    {
        $satuan = Satuan::find($id);
        if (!$satuan) {
            return response()->json(['status' => false, 'message' => 'Satuan tidak ditemukan.'], 404);
        }
        $satuan->update($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil diperbarui.',
            'data'    => new SatuanResource($satuan),
        ]);
    }

    /**
     * Hapus satuan.
     *
     * @OA\Delete(path="/satuan/{id}", operationId="destroySatuan", tags={"Satuan"}, summary="Hapus satuan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/SatuanDeleteResponse"))
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $satuan = Satuan::find($id);
        if (!$satuan) {
            return response()->json(['status' => false, 'message' => 'Satuan tidak ditemukan.'], 404);
        }
        $satuan->delete();
        return response()->json(['status' => true, 'message' => 'Satuan berhasil dihapus.']);
    }
}
