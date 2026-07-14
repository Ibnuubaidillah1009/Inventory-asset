<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMapelRequest;
use App\Http\Requests\UpdateMapelRequest;
use App\Http\Resources\MapelResource;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Mapel Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="MapelResource",
 *     type="object",
 *     description="Representasi data mata pelajaran",
 *     @OA\Property(property="id_mapel", type="integer", example=1),
 *     @OA\Property(property="nama_mapel", type="string", example="Matematika")
 * )
 *
 * @OA\Schema(
 *     schema="StoreMapelRequest",
 *     type="object",
 *     required={"nama_mapel"},
 *     description="Payload untuk menambah mata pelajaran baru",
 *     @OA\Property(property="nama_mapel", type="string", maxLength=100, example="Matematika")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateMapelRequest",
 *     type="object",
 *     description="Payload untuk memperbarui mata pelajaran",
 *     @OA\Property(property="nama_mapel", type="string", maxLength=100, example="Fisika")
 * )
 *
 * @OA\Schema(
 *     schema="MapelListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar mapel",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar mapel berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MapelResource"))
 * )
 *
 * @OA\Schema(
 *     schema="MapelSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu mapel",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail mapel berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/MapelResource")
 * )
 *
 * @OA\Schema(
 *     schema="MapelDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan mapel",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Mapel berhasil dihapus.")
 * )
 */
class MapelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/mapel",
     *     operationId="indexMapel",
     *     tags={"Mapel"},
     *     summary="Daftar semua mata pelajaran",
     *     description="Mengambil daftar semua mata pelajaran.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar mapel berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/MapelListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => 'Daftar mapel berhasil diambil.',
            'data'    => MapelResource::collection(Mapel::all()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/mapel",
     *     operationId="storeMapel",
     *     tags={"Mapel"},
     *     summary="Tambah mata pelajaran baru",
     *     description="Menyimpan data mata pelajaran baru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreMapelRequest")
     *     ),
     *     @OA\Response(response=201, description="Mapel berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/MapelSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreMapelRequest $request): JsonResponse
    {
        $mapel = Mapel::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Mapel berhasil ditambahkan.',
            'data'    => new MapelResource($mapel),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/mapel/{id}",
     *     operationId="showMapel",
     *     tags={"Mapel"},
     *     summary="Detail mata pelajaran",
     *     description="Mengambil detail satu mata pelajaran berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID mapel", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Detail mapel berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/MapelSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Mapel tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'status'  => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail mapel berhasil diambil.',
            'data'    => new MapelResource($mapel),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/mapel/{id}",
     *     operationId="updateMapel",
     *     tags={"Mapel"},
     *     summary="Update mata pelajaran",
     *     description="Memperbarui data mata pelajaran berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID mapel", @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateMapelRequest")
     *     ),
     *     @OA\Response(response=200, description="Mapel berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/MapelSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Mapel tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateMapelRequest $request, string $id): JsonResponse
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'status'  => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        }

        $mapel->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Mapel berhasil diperbarui.',
            'data'    => new MapelResource($mapel),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/mapel/{id}",
     *     operationId="destroyMapel",
     *     tags={"Mapel"},
     *     summary="Hapus mata pelajaran",
     *     description="Menghapus data mata pelajaran berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID mapel", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Mapel berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/MapelDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Mapel tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'status'  => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        }

        $mapel->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Mapel berhasil dihapus.',
        ]);
    }
}
