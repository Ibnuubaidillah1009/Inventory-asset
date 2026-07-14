<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePemasokRequest;
use App\Http\Requests\UpdatePemasokRequest;
use App\Http\Resources\PemasokResource;
use App\Models\Pemasok;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Pemasok (Supplier) Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="PemasokResource",
 *     type="object",
 *     description="Representasi data pemasok / supplier",
 *     @OA\Property(property="id_pemasok", type="integer", example=1),
 *     @OA\Property(property="nama_pemasok", type="string", example="PT Sumber Jaya"),
 *     @OA\Property(property="nomor_telepon", type="string", nullable=true, example="081234567890"),
 *     @OA\Property(property="alamat", type="string", nullable=true, example="Jl. Raya No. 123"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Pemasok alat tulis")
 * )
 *
 * @OA\Schema(
 *     schema="StorePemasokRequest",
 *     type="object",
 *     required={"nama_pemasok"},
 *     description="Payload untuk menambah pemasok baru",
 *     @OA\Property(property="id_pemasok", type="integer", nullable=true, example=100, description="Opsional. Jika dikosongkan akan di-generate otomatis."),
 *     @OA\Property(property="nama_pemasok", type="string", maxLength=150, example="PT Sumber Jaya"),
 *     @OA\Property(property="nomor_telepon", type="string", maxLength=20, nullable=true, example="081234567890"),
 *     @OA\Property(property="alamat", type="string", nullable=true, example="Jl. Raya No. 123"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Pemasok alat tulis")
 * )
 *
 * @OA\Schema(
 *     schema="UpdatePemasokRequest",
 *     type="object",
 *     description="Payload untuk memperbarui pemasok",
 *     @OA\Property(property="nama_pemasok", type="string", maxLength=150, example="PT Sumber Jaya Updated"),
 *     @OA\Property(property="nomor_telepon", type="string", maxLength=20, nullable=true, example="089876543210"),
 *     @OA\Property(property="alamat", type="string", nullable=true, example="Jl. Baru No. 456"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Update keterangan")
 * )
 *
 * @OA\Schema(
 *     schema="PemasokListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar pemasok",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar pemasok berhasil diambil."),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/PemasokResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PemasokSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu pemasok",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail pemasok berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PemasokResource")
 * )
 *
 * @OA\Schema(
 *     schema="PemasokDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan pemasok",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Pemasok berhasil dihapus.")
 * )
 */
class PemasokController extends Controller
{
    /**
     * @OA\Get(
     *     path="/pemasok",
     *     operationId="indexPemasok",
     *     tags={"Pemasok"},
     *     summary="Daftar semua pemasok",
     *     description="Mengambil daftar semua pemasok / supplier.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar pemasok berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/PemasokListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => 'Daftar pemasok berhasil diambil.',
            'data'    => PemasokResource::collection(Pemasok::all()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/pemasok",
     *     operationId="storePemasok",
     *     tags={"Pemasok"},
     *     summary="Tambah pemasok baru",
     *     description="Menyimpan data pemasok baru. Jika id_pemasok dikosongkan, sistem akan men-generate ID unik secara acak.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePemasokRequest")
     *     ),
     *     @OA\Response(response=201, description="Pemasok berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/PemasokSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePemasokRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Jika id_pemasok dikosongkan, generate angka acak yang unik
        if (empty($data['id_pemasok'])) {
            do {
                $randomId = random_int(1000, 999999);
            } while (Pemasok::where('id_pemasok', $randomId)->exists());

            $data['id_pemasok'] = $randomId;
        }

        $pemasok = Pemasok::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Pemasok berhasil ditambahkan.',
            'data'    => new PemasokResource($pemasok),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/pemasok/{id}",
     *     operationId="showPemasok",
     *     tags={"Pemasok"},
     *     summary="Detail pemasok",
     *     description="Mengambil detail satu pemasok berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID pemasok", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Detail pemasok berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/PemasokSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Pemasok tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $pemasok = Pemasok::find($id);

        if (!$pemasok) {
            return response()->json([
                'status'  => false,
                'message' => 'Pemasok tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail pemasok berhasil diambil.',
            'data'    => new PemasokResource($pemasok),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/pemasok/{id}",
     *     operationId="updatePemasok",
     *     tags={"Pemasok"},
     *     summary="Update pemasok",
     *     description="Memperbarui data pemasok berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID pemasok", @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePemasokRequest")
     *     ),
     *     @OA\Response(response=200, description="Pemasok berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/PemasokSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Pemasok tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdatePemasokRequest $request, string $id): JsonResponse
    {
        $pemasok = Pemasok::find($id);

        if (!$pemasok) {
            return response()->json([
                'status'  => false,
                'message' => 'Pemasok tidak ditemukan.',
            ], 404);
        }

        $pemasok->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Pemasok berhasil diperbarui.',
            'data'    => new PemasokResource($pemasok),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/pemasok/{id}",
     *     operationId="destroyPemasok",
     *     tags={"Pemasok"},
     *     summary="Hapus pemasok",
     *     description="Menghapus data pemasok berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID pemasok", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Pemasok berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/PemasokDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Pemasok tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $pemasok = Pemasok::find($id);

        if (!$pemasok) {
            return response()->json([
                'status'  => false,
                'message' => 'Pemasok tidak ditemukan.',
            ], 404);
        }

        $pemasok->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Pemasok berhasil dihapus.',
        ]);
    }
}
