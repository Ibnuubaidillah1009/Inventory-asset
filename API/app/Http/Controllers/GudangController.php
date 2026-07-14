<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGudangRequest;
use App\Http\Requests\UpdateGudangRequest;
use App\Http\Resources\GudangResource;
use App\Models\Gudang;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Gudang (Warehouse) Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="GudangResource",
 *     type="object",
 *     description="Representasi data gudang",
 *     @OA\Property(property="kode_gudang", type="string", example="GDG-001"),
 *     @OA\Property(property="nama_gudang", type="string", example="Gudang Utama"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Gudang penyimpanan barang inventaris")
 * )
 *
 * @OA\Schema(
 *     schema="StoreGudangRequest",
 *     type="object",
 *     required={"kode_gudang", "nama_gudang"},
 *     description="Payload untuk menambah gudang baru",
 *     @OA\Property(property="kode_gudang", type="string", maxLength=20, example="GDG-001"),
 *     @OA\Property(property="nama_gudang", type="string", maxLength=100, example="Gudang Utama"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Gudang penyimpanan barang inventaris")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateGudangRequest",
 *     type="object",
 *     description="Payload untuk memperbarui gudang",
 *     @OA\Property(property="nama_gudang", type="string", maxLength=100, example="Gudang Utama Updated"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Keterangan baru")
 * )
 *
 * @OA\Schema(
 *     schema="GudangListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar gudang",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar gudang berhasil diambil."),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/GudangResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="GudangSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu gudang",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail gudang berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/GudangResource")
 * )
 *
 * @OA\Schema(
 *     schema="GudangDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan gudang",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Gudang berhasil dihapus.")
 * )
 */
class GudangController extends Controller
{
    /**
     * @OA\Get(
     *     path="/gudang",
     *     operationId="indexGudang",
     *     tags={"Gudang"},
     *     summary="Daftar semua gudang",
     *     description="Mengambil daftar semua gudang.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar gudang berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/GudangListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => 'Daftar gudang berhasil diambil.',
            'data'    => GudangResource::collection(Gudang::all()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/gudang",
     *     operationId="storeGudang",
     *     tags={"Gudang"},
     *     summary="Tambah gudang baru",
     *     description="Menyimpan data gudang baru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreGudangRequest")
     *     ),
     *     @OA\Response(response=201, description="Gudang berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/GudangSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreGudangRequest $request): JsonResponse
    {
        $gudang = Gudang::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Gudang berhasil ditambahkan.',
            'data'    => new GudangResource($gudang),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/gudang/{id}",
     *     operationId="showGudang",
     *     tags={"Gudang"},
     *     summary="Detail gudang",
     *     description="Mengambil detail satu gudang berdasarkan kode gudang.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Kode gudang", @OA\Schema(type="string", example="GDG-001")),
     *     @OA\Response(response=200, description="Detail gudang berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/GudangSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Gudang tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $gudang = Gudang::find($id);

        if (!$gudang) {
            return response()->json([
                'status'  => false,
                'message' => 'Gudang tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail gudang berhasil diambil.',
            'data'    => new GudangResource($gudang),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/gudang/{id}",
     *     operationId="updateGudang",
     *     tags={"Gudang"},
     *     summary="Update gudang",
     *     description="Memperbarui data gudang berdasarkan kode gudang.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Kode gudang", @OA\Schema(type="string", example="GDG-001")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateGudangRequest")
     *     ),
     *     @OA\Response(response=200, description="Gudang berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/GudangSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Gudang tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateGudangRequest $request, string $id): JsonResponse
    {
        $gudang = Gudang::find($id);

        if (!$gudang) {
            return response()->json([
                'status'  => false,
                'message' => 'Gudang tidak ditemukan.',
            ], 404);
        }

        $gudang->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Gudang berhasil diperbarui.',
            'data'    => new GudangResource($gudang),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/gudang/{id}",
     *     operationId="destroyGudang",
     *     tags={"Gudang"},
     *     summary="Hapus gudang",
     *     description="Menghapus data gudang berdasarkan kode gudang.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Kode gudang", @OA\Schema(type="string", example="GDG-001")),
     *     @OA\Response(response=200, description="Gudang berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/GudangDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Gudang tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $gudang = Gudang::find($id);

        if (!$gudang) {
            return response()->json([
                'status'  => false,
                'message' => 'Gudang tidak ditemukan.',
            ], 404);
        }

        $gudang->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Gudang berhasil dihapus.',
        ]);
    }
}
