<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKondisiRequest;
use App\Http\Requests\UpdateKondisiRequest;
use App\Http\Resources\KondisiResource;
use App\Models\Kondisi;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Kondisi Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="KondisiResource",
 *     type="object",
 *     description="Representasi data kondisi barang",
 *     @OA\Property(property="id_kondisi", type="integer", example=1),
 *     @OA\Property(property="nama_kondisi", type="string", example="Baik"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Kondisi barang baik")
 * )
 *
 * @OA\Schema(
 *     schema="StoreKondisiRequest",
 *     type="object",
 *     required={"nama_kondisi"},
 *     description="Payload untuk menambah kondisi baru",
 *     @OA\Property(property="nama_kondisi", type="string", maxLength=100, example="Baik"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Kondisi barang baik")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateKondisiRequest",
 *     type="object",
 *     description="Payload untuk memperbarui kondisi",
 *     @OA\Property(property="nama_kondisi", type="string", maxLength=100, example="Rusak Ringan"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Kondisi rusak ringan")
 * )
 *
 * @OA\Schema(
 *     schema="KondisiListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar kondisi",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar kondisi berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/KondisiResource"))
 * )
 *
 * @OA\Schema(
 *     schema="KondisiSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu kondisi",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail kondisi berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/KondisiResource")
 * )
 *
 * @OA\Schema(
 *     schema="KondisiDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan kondisi",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Kondisi berhasil dihapus.")
 * )
 */
class AsetKondisiController extends Controller
{
    /**
     * Tampilkan daftar semua kondisi.
     *
     * @OA\Get(
     *     path="/aset-kondisi",
     *     operationId="indexKondisi",
     *     tags={"Kondisi"},
     *     summary="Daftar kondisi",
     *     description="Mengambil daftar semua data kondisi barang.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar kondisi berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KondisiListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        $kondisi = Kondisi::all();
        return response()->json([
            'status'  => true,
            'message' => 'Daftar kondisi berhasil diambil.',
            'data'    => KondisiResource::collection($kondisi),
        ]);
    }

    /**
     * Simpan kondisi baru.
     *
     * @OA\Post(
     *     path="/aset-kondisi",
     *     operationId="storeKondisi",
     *     tags={"Kondisi"},
     *     summary="Tambah kondisi",
     *     description="Menyimpan data kondisi baru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreKondisiRequest")
     *     ),
     *     @OA\Response(response=201, description="Kondisi berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/KondisiSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreKondisiRequest $request): JsonResponse
    {
        $kondisi = Kondisi::create($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'Kondisi berhasil ditambahkan.',
            'data'    => new KondisiResource($kondisi),
        ], 201);
    }

    /**
     * Tampilkan detail kondisi.
     *
     * @OA\Get(
     *     path="/aset-kondisi/{id}",
     *     operationId="showKondisi",
     *     tags={"Kondisi"},
     *     summary="Detail kondisi",
     *     description="Mengambil detail satu kondisi.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kondisi", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Detail kondisi berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KondisiSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Kondisi tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $kondisi = Kondisi::find($id);
        if (!$kondisi) {
            return response()->json(['status' => false, 'message' => 'Kondisi tidak ditemukan.'], 404);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Detail kondisi berhasil diambil.',
            'data'    => new KondisiResource($kondisi),
        ]);
    }

    /**
     * Update kondisi.
     *
     * @OA\Put(
     *     path="/aset-kondisi/{id}",
     *     operationId="updateKondisi",
     *     tags={"Kondisi"},
     *     summary="Update kondisi",
     *     description="Memperbarui data kondisi.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kondisi", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateKondisiRequest")
     *     ),
     *     @OA\Response(response=200, description="Kondisi berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/KondisiSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Kondisi tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateKondisiRequest $request, int $id): JsonResponse
    {
        $kondisi = Kondisi::find($id);
        if (!$kondisi) {
            return response()->json(['status' => false, 'message' => 'Kondisi tidak ditemukan.'], 404);
        }
        $kondisi->update($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'Kondisi berhasil diperbarui.',
            'data'    => new KondisiResource($kondisi),
        ]);
    }

    /**
     * Hapus kondisi.
     *
     * @OA\Delete(
     *     path="/aset-kondisi/{id}",
     *     operationId="destroyKondisi",
     *     tags={"Kondisi"},
     *     summary="Hapus kondisi",
     *     description="Menghapus kondisi.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kondisi", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Kondisi berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/KondisiDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Kondisi tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $kondisi = Kondisi::find($id);
        if (!$kondisi) {
            return response()->json(['status' => false, 'message' => 'Kondisi tidak ditemukan.'], 404);
        }
        $kondisi->delete();
        return response()->json(['status' => true, 'message' => 'Kondisi berhasil dihapus.']);
    }
}
