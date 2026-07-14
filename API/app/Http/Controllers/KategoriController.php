<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Kategori Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="KategoriResource",
 *     type="object",
 *     description="Representasi data kategori barang",
 *     @OA\Property(property="id_kategori", type="integer", example=1),
 *     @OA\Property(property="nama_kategori", type="string", example="Elektronik"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Barang-barang elektronik")
 * )
 *
 * @OA\Schema(
 *     schema="StoreKategoriRequest",
 *     type="object",
 *     required={"nama_kategori"},
 *     description="Payload untuk menambah kategori baru",
 *     @OA\Property(property="nama_kategori", type="string", maxLength=100, example="Elektronik"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Barang-barang elektronik")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateKategoriRequest",
 *     type="object",
 *     description="Payload untuk memperbarui kategori",
 *     @OA\Property(property="nama_kategori", type="string", maxLength=100, example="Furnitur"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Perabotan kantor")
 * )
 *
 * @OA\Schema(
 *     schema="KategoriListResponse",
 *     type="object",
 *     description="Response wrapper untuk daftar kategori",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar kategori berhasil diambil."),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/KategoriResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="KategoriSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu kategori",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail kategori berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/KategoriResource")
 * )
 *
 * @OA\Schema(
 *     schema="KategoriDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan kategori",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Kategori berhasil dihapus.")
 * )
 */
class KategoriController extends Controller
{
    /**
     * @OA\Get(
     *     path="/kategori",
     *     operationId="indexKategori",
     *     tags={"Kategori"},
     *     summary="Daftar semua kategori",
     *     description="Mengambil daftar semua kategori barang.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar kategori berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KategoriListResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $query = Kategori::query();
        
        if ($search) {
            $query->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('id_kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
        }

        $kategori = $query->paginate(20);
        return KategoriResource::collection($kategori)->additional([
            'status'  => true,
            'message' => 'Daftar kategori berhasil diambil.'
        ])->response();
    }

    /**
     * @OA\Post(
     *     path="/kategori",
     *     operationId="storeKategori",
     *     tags={"Kategori"},
     *     summary="Tambah kategori baru",
     *     description="Menyimpan data kategori barang baru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreKategoriRequest")
     *     ),
     *     @OA\Response(response=201, description="Kategori berhasil ditambahkan",
     *         @OA\JsonContent(ref="#/components/schemas/KategoriSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreKategoriRequest $request): JsonResponse
    {
        $kategori = Kategori::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data'    => new KategoriResource($kategori),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/kategori/{id}",
     *     operationId="showKategori",
     *     tags={"Kategori"},
     *     summary="Detail kategori",
     *     description="Mengambil detail satu kategori berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kategori", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Detail kategori berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/KategoriSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kategori tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $kategori = Kategori::find($id);

        if (!$kategori)  {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail kategori berhasil diambil.',
            'data'    => new KategoriResource($kategori),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/kategori/{id}",
     *     operationId="updateKategori",
     *     tags={"Kategori"},
     *     summary="Update kategori",
     *     description="Memperbarui data kategori berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kategori", @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateKategoriRequest")
     *     ),
     *     @OA\Response(response=200, description="Kategori berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/KategoriSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kategori tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateKategoriRequest $request, string $id): JsonResponse
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        $kategori->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data'    => new KategoriResource($kategori),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/kategori/{id}",
     *     operationId="destroyKategori",
     *     tags={"Kategori"},
     *     summary="Hapus kategori",
     *     description="Menghapus data kategori berdasarkan ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID kategori", @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="Kategori berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/KategoriDeleteResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Kategori tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        $kategori->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
