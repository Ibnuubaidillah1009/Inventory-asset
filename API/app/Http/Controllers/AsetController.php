<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Http\Requests\StoreAsetRequest;
use App\Http\Requests\UpdateAsetRequest;
use App\Http\Resources\AsetResource;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="AsetResource", type="object",
 *     description="Representasi data aset inventaris",
 *     @OA\Property(property="kode_barang", type="string", example="ASET-2026-001"),
 *     @OA\Property(property="id_master_barang", type="integer", example=1),
 *     @OA\Property(property="master_barang", ref="#/components/schemas/MasterBarangResource", nullable=true),
 *     @OA\Property(property="tanggal_registrasi", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="gambar", type="string", nullable=true, example=null),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example=null),
 *     @OA\Property(property="aset_bangunan", ref="#/components/schemas/AsetBangunanResource", nullable=true),
 *     @OA\Property(property="aset_tanah", ref="#/components/schemas/AsetTanahResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreAsetRequest", type="object", required={"kode_barang","id_master_barang","tanggal_registrasi"},
 *     description="Payload untuk menambah aset baru",
 *     @OA\Property(property="kode_barang", type="string", example="ASET-2026-005"),
 *     @OA\Property(property="id_master_barang", type="integer", example=1),
 *     @OA\Property(property="tanggal_registrasi", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="gambar", type="string", nullable=true),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdateAsetRequest", type="object",
 *     description="Payload untuk memperbarui aset",
 *     @OA\Property(property="kode_barang", type="string", example="ASET-2026-005"),
 *     @OA\Property(property="id_master_barang", type="integer", example=1),
 *     @OA\Property(property="tanggal_registrasi", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="gambar", type="string", nullable=true),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="AsetListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar aset berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AsetResource"))
 * )
 * @OA\Schema(schema="AsetSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail aset berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/AsetResource")
 * )
 * @OA\Schema(schema="AsetDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Aset berhasil dihapus.")
 * )
 */
class AsetController extends Controller
{
    /**
     * @OA\Get(path="/aset", operationId="indexAset", tags={"Aset"}, summary="Daftar aset", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Aset::with(['masterBarang.kategori', 'masterBarang.merek', 'masterBarang.satuan'])->get();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar aset berhasil diambil.',
            'data'    => AsetResource::collection($data),
        ]);
    }

    /**
     * @OA\Post(path="/aset", operationId="storeAset", tags={"Aset"}, summary="Tambah aset", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreAsetRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/AsetSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreAsetRequest $request): JsonResponse
    {
        $aset = Aset::create($request->validated());
        $aset->load(['masterBarang']);

        return response()->json([
            'status'  => true,
            'message' => 'Aset berhasil ditambahkan.',
            'data'    => new AsetResource($aset),
        ], 201);
    }

    /**
     * @OA\Get(path="/aset/{id}", operationId="showAset", tags={"Aset"}, summary="Detail aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Kode barang", @OA\Schema(type="string", example="ASET-2026-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetSingleResponse")),
     *     @OA\Response(response=404, description="Aset tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $aset = Aset::with(['masterBarang.kategori', 'masterBarang.merek', 'masterBarang.satuan', 'asetBangunan', 'asetTanah'])->find($id);

        if (!$aset) {
            return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail aset berhasil diambil.',
            'data'    => new AsetResource($aset),
        ]);
    }

    /**
     * @OA\Put(path="/aset/{id}", operationId="updateAset", tags={"Aset"}, summary="Update aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Kode barang", @OA\Schema(type="string", example="ASET-2026-001")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateAsetRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetSingleResponse")),
     *     @OA\Response(response=404, description="Aset tidak ditemukan")
     * )
     */
    public function update(UpdateAsetRequest $request, string $id): JsonResponse
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        $aset->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Aset berhasil diperbarui.',
            'data'    => new AsetResource($aset->fresh(['masterBarang'])),
        ]);
    }

    /**
     * @OA\Delete(path="/aset/{id}", operationId="destroyAset", tags={"Aset"}, summary="Hapus aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Kode barang", @OA\Schema(type="string", example="ASET-2026-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AsetDeleteResponse")),
     *     @OA\Response(response=404, description="Aset tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        $aset->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Aset berhasil dihapus.',
        ]);
    }
}
