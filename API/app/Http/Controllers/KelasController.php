<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Http\Resources\KelasResource;
use App\Models\Kelas;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Kelas Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="KelasResource",
 *     type="object",
 *     description="Representasi data kelas",
 *     @OA\Property(property="id_kelas", type="integer", example=1),
 *     @OA\Property(property="nama_kelas", type="string", example="Kelas A"),
 *     @OA\Property(property="id_rombel", type="integer", example=1),
 *     @OA\Property(property="rombel", ref="#/components/schemas/RombelResource", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="StoreKelasRequest",
 *     type="object",
 *     required={"nama_kelas","id_rombel"},
 *     description="Payload untuk menambah kelas baru",
 *     @OA\Property(property="nama_kelas", type="string", maxLength=100, example="Kelas A"),
 *     @OA\Property(property="id_rombel", type="integer", example=1)
 * )
 *
 * @OA\Schema(
 *     schema="UpdateKelasRequest",
 *     type="object",
 *     description="Payload untuk memperbarui kelas",
 *     @OA\Property(property="nama_kelas", type="string", maxLength=100, example="Kelas B"),
 *     @OA\Property(property="id_rombel", type="integer", example=2)
 * )
 *
 * @OA\Schema(
 *     schema="KelasListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar kelas",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar kelas berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/KelasResource"))
 * )
 *
 * @OA\Schema(
 *     schema="KelasSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu kelas",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail kelas berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/KelasResource")
 * )
 *
 * @OA\Schema(
 *     schema="KelasDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan kelas",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Kelas berhasil dihapus.")
 * )
 */
class KelasController extends Controller
{
    /**
     * Tampilkan daftar semua kelas beserta relasi rombel.
     *
     * @OA\Get(
     *     path="/kelas",
     *     operationId="indexKelas",
     *     tags={"Kelas"},
     *     summary="Daftar semua kelas",
     *     description="Mengambil daftar semua kelas beserta relasi rombel dan jurusan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar kelas berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KelasListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        $kelas = Kelas::with('rombel.jurusan')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar kelas berhasil diambil.',
            'data'    => KelasResource::collection($kelas),
        ]);
    }

    /**
     * Simpan kelas baru.
     *
     * @OA\Post(
     *     path="/kelas",
     *     operationId="storeKelas",
     *     tags={"Kelas"},
     *     summary="Tambah kelas baru",
     *     description="Menyimpan data kelas baru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreKelasRequest")
     *     ),
     *     @OA\Response(response=201, description="Kelas berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/KelasSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreKelasRequest $request): JsonResponse
    {
        $kelas = Kelas::create($request->validated());
        $kelas->load('rombel.jurusan');

        return response()->json([
            'status'  => true,
            'message' => 'Kelas berhasil ditambahkan.',
            'data'    => new KelasResource($kelas),
        ], 201);
    }

    /**
     * Tampilkan detail satu kelas.
     *
     * @OA\Get(
     *     path="/kelas/{id}",
     *     operationId="showKelas",
     *     tags={"Kelas"},
     *     summary="Detail kelas",
     *     description="Mengambil detail satu kelas beserta rombel dan jurusan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kelas", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Detail kelas berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KelasSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kelas tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $kelas = Kelas::with('rombel.jurusan')->find($id);

        if (!$kelas) {
            return response()->json([
                'status'  => false,
                'message' => 'Kelas tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail kelas berhasil diambil.',
            'data'    => new KelasResource($kelas),
        ]);
    }

    /**
     * Update data kelas.
     *
     * @OA\Put(
     *     path="/kelas/{id}",
     *     operationId="updateKelas",
     *     tags={"Kelas"},
     *     summary="Update kelas",
     *     description="Memperbarui data kelas berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kelas", @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateKelasRequest")
     *     ),
     *     @OA\Response(response=200, description="Kelas berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/KelasSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kelas tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateKelasRequest $request, string $id): JsonResponse
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'status'  => false,
                'message' => 'Kelas tidak ditemukan.',
            ], 404);
        }

        $kelas->update($request->validated());
        $kelas->load('rombel.jurusan');

        return response()->json([
            'status'  => true,
            'message' => 'Kelas berhasil diperbarui.',
            'data'    => new KelasResource($kelas),
        ]);
    }

    /**
     * Hapus kelas.
     *
     * @OA\Delete(
     *     path="/kelas/{id}",
     *     operationId="destroyKelas",
     *     tags={"Kelas"},
     *     summary="Hapus kelas",
     *     description="Menghapus data kelas berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kelas", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Kelas berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/KelasDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kelas tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'status'  => false,
                'message' => 'Kelas tidak ditemukan.',
            ], 404);
        }

        $kelas->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Kelas berhasil dihapus.',
        ]);
    }
}
