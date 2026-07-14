<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJurusanRequest;
use App\Http\Requests\UpdateJurusanRequest;
use App\Http\Resources\JurusanResource;
use App\Models\Jurusan;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Jurusan Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="JurusanResource",
 *     type="object",
 *     description="Representasi data jurusan",
 *     @OA\Property(property="id_jurusan", type="integer", example=1),
 *     @OA\Property(property="nama_jurusan", type="string", example="Teknik Komputer dan Jaringan"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Jurusan TKJ")
 * )
 *
 * @OA\Schema(
 *     schema="StoreJurusanRequest",
 *     type="object",
 *     required={"nama_jurusan"},
 *     description="Payload untuk menambah jurusan baru",
 *     @OA\Property(property="nama_jurusan", type="string", maxLength=100, example="Teknik Komputer dan Jaringan"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Jurusan TKJ")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateJurusanRequest",
 *     type="object",
 *     description="Payload untuk memperbarui jurusan",
 *     @OA\Property(property="nama_jurusan", type="string", maxLength=100, example="Rekayasa Perangkat Lunak"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Jurusan RPL")
 * )
 *
 * @OA\Schema(
 *     schema="JurusanListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar jurusan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar jurusan berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/JurusanResource"))
 * )
 *
 * @OA\Schema(
 *     schema="JurusanSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu jurusan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail jurusan berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/JurusanResource")
 * )
 *
 * @OA\Schema(
 *     schema="JurusanDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan jurusan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Jurusan berhasil dihapus.")
 * )
 */
class JurusanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/jurusan",
     *     operationId="indexJurusan",
     *     tags={"Jurusan"},
     *     summary="Daftar semua jurusan",
     *     description="Mengambil daftar semua jurusan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar jurusan berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/JurusanListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => 'Daftar jurusan berhasil diambil.',
            'data'    => JurusanResource::collection(Jurusan::all()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/jurusan",
     *     operationId="storeJurusan",
     *     tags={"Jurusan"},
     *     summary="Tambah jurusan baru",
     *     description="Menyimpan data jurusan baru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreJurusanRequest")
     *     ),
     *     @OA\Response(response=201, description="Jurusan berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/JurusanSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreJurusanRequest $request): JsonResponse
    {
        $jurusan = Jurusan::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Jurusan berhasil ditambahkan.',
            'data'    => new JurusanResource($jurusan),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/jurusan/{id}",
     *     operationId="showJurusan",
     *     tags={"Jurusan"},
     *     summary="Detail jurusan",
     *     description="Mengambil detail satu jurusan berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID jurusan", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Detail jurusan berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/JurusanSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Jurusan tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $jurusan = Jurusan::find($id);

        if (!$jurusan) {
            return response()->json([
                'status'  => false,
                'message' => 'Jurusan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail jurusan berhasil diambil.',
            'data'    => new JurusanResource($jurusan),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/jurusan/{id}",
     *     operationId="updateJurusan",
     *     tags={"Jurusan"},
     *     summary="Update jurusan",
     *     description="Memperbarui data jurusan berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID jurusan", @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateJurusanRequest")
     *     ),
     *     @OA\Response(response=200, description="Jurusan berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/JurusanSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Jurusan tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateJurusanRequest $request, string $id): JsonResponse
    {
        $jurusan = Jurusan::find($id);

        if (!$jurusan) {
            return response()->json([
                'status'  => false,
                'message' => 'Jurusan tidak ditemukan.',
            ], 404);
        }

        $jurusan->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Jurusan berhasil diperbarui.',
            'data'    => new JurusanResource($jurusan),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/jurusan/{id}",
     *     operationId="destroyJurusan",
     *     tags={"Jurusan"},
     *     summary="Hapus jurusan",
     *     description="Menghapus data jurusan berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID jurusan", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Jurusan berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/JurusanDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Jurusan tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $jurusan = Jurusan::find($id);

        if (!$jurusan) {
            return response()->json([
                'status'  => false,
                'message' => 'Jurusan tidak ditemukan.',
            ], 404);
        }

        $jurusan->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Jurusan berhasil dihapus.',
        ]);
    }
}
