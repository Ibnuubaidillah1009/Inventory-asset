<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAksesRequest;
use App\Http\Requests\UpdateAksesRequest;
use App\Http\Resources\AksesResource;
use App\Models\Akses;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Akses Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="AksesResource",
 *     type="object",
 *     description="Representasi data modul akses / permission",
 *     @OA\Property(property="id_akses", type="integer", example=1),
 *     @OA\Property(property="nama_modul", type="string", example="inventaris"),
 *     @OA\Property(property="hak_buat", type="boolean", example=true),
 *     @OA\Property(property="hak_baca", type="boolean", example=true),
 *     @OA\Property(property="hak_ubah", type="boolean", example=true),
 *     @OA\Property(property="hak_hapus", type="boolean", example=false)
 * )
 *
 * @OA\Schema(
 *     schema="StoreAksesRequest",
 *     type="object",
 *     required={"nama_modul"},
 *     description="Payload untuk menambah modul akses baru",
 *     @OA\Property(property="nama_modul", type="string", maxLength=100, example="inventaris"),
 *     @OA\Property(property="hak_buat", type="boolean", example=true),
 *     @OA\Property(property="hak_baca", type="boolean", example=true),
 *     @OA\Property(property="hak_ubah", type="boolean", example=true),
 *     @OA\Property(property="hak_hapus", type="boolean", example=false)
 * )
 *
 * @OA\Schema(
 *     schema="UpdateAksesRequest",
 *     type="object",
 *     description="Payload untuk memperbarui modul akses",
 *     @OA\Property(property="nama_modul", type="string", maxLength=100, example="inventaris"),
 *     @OA\Property(property="hak_buat", type="boolean", example=true),
 *     @OA\Property(property="hak_baca", type="boolean", example=true),
 *     @OA\Property(property="hak_ubah", type="boolean", example=false),
 *     @OA\Property(property="hak_hapus", type="boolean", example=false)
 * )
 *
 * @OA\Schema(
 *     schema="AksesListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar akses",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar akses berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AksesResource"))
 * )
 *
 * @OA\Schema(
 *     schema="AksesSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu akses",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail akses berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/AksesResource")
 * )
 *
 * @OA\Schema(
 *     schema="AksesDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan akses",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Akses berhasil dihapus.")
 * )
 */
class AksesController extends Controller
{
    /**
     * Tampilkan daftar semua modul akses.
     *
     * @OA\Get(
     *     path="/akses",
     *     operationId="indexAkses",
     *     tags={"Akses"},
     *     summary="Daftar semua modul akses",
     *     description="Mengambil daftar semua modul akses / permission.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar akses berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/AksesListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden — tidak memiliki hak akses")
     * )
     */
    public function index(): JsonResponse
    {
        $akses = Akses::all();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar akses berhasil diambil.',
            'data'    => AksesResource::collection($akses),
        ]);
    }

    /**
     * Simpan akses/modul baru.
     *
     * @OA\Post(
     *     path="/akses",
     *     operationId="storeAkses",
     *     tags={"Akses"},
     *     summary="Tambah modul akses baru",
     *     description="Menyimpan data modul akses baru beserta hak CRUD-nya.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAksesRequest")
     *     ),
     *     @OA\Response(response=201, description="Akses berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/AksesSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreAksesRequest $request): JsonResponse
    {
        $akses = Akses::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Akses berhasil ditambahkan.',
            'data'    => new AksesResource($akses),
        ], 201);
    }

    /**
     * Tampilkan detail satu akses.
     *
     * @OA\Get(
     *     path="/akses/{id}",
     *     operationId="showAkses",
     *     tags={"Akses"},
     *     summary="Detail modul akses",
     *     description="Mengambil detail satu modul akses berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID akses", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Detail akses berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/AksesSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Akses tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $akses = Akses::find($id);

        if (!$akses) {
            return response()->json([
                'status'  => false,
                'message' => 'Akses tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail akses berhasil diambil.',
            'data'    => new AksesResource($akses),
        ]);
    }

    /**
     * Update data akses.
     *
     * @OA\Put(
     *     path="/akses/{id}",
     *     operationId="updateAkses",
     *     tags={"Akses"},
     *     summary="Update modul akses",
     *     description="Memperbarui data modul akses berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID akses", @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAksesRequest")
     *     ),
     *     @OA\Response(response=200, description="Akses berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/AksesSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Akses tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateAksesRequest $request, string $id): JsonResponse
    {
        $akses = Akses::find($id);

        if (!$akses) {
            return response()->json([
                'status'  => false,
                'message' => 'Akses tidak ditemukan.',
            ], 404);
        }

        $akses->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Akses berhasil diperbarui.',
            'data'    => new AksesResource($akses),
        ]);
    }

    /**
     * Hapus akses.
     *
     * @OA\Delete(
     *     path="/akses/{id}",
     *     operationId="destroyAkses",
     *     tags={"Akses"},
     *     summary="Hapus modul akses",
     *     description="Menghapus data modul akses berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID akses", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Akses berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/AksesDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Akses tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $akses = Akses::find($id);

        if (!$akses) {
            return response()->json([
                'status'  => false,
                'message' => 'Akses tidak ditemukan.',
            ], 404);
        }

        $akses->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Akses berhasil dihapus.',
        ]);
    }
}
