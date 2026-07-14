<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterBarangRequest;
use App\Http\Requests\UpdateMasterBarangRequest;
use App\Http\Resources\MasterBarangResource;
use App\Models\MasterBarang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(schema="MasterBarangResource", type="object",
 *     description="Representasi data master barang",
 *     @OA\Property(property="id_master_barang", type="integer", example=1),
 *     @OA\Property(property="nama_barang", type="string", example="Laptop Lenovo ThinkPad"),
 *     @OA\Property(property="id_kategori", type="integer", example=1),
 *     @OA\Property(property="kategori", ref="#/components/schemas/KategoriResource", nullable=true),
 *     @OA\Property(property="id_merek", type="integer", example=1),
 *     @OA\Property(property="merek", ref="#/components/schemas/MerekResource", nullable=true),
 *     @OA\Property(property="id_satuan", type="integer", example=1),
 *     @OA\Property(property="satuan", ref="#/components/schemas/SatuanResource", nullable=true),
 *     @OA\Property(property="jenis_barang", type="string", example="Inventaris"),
 *     @OA\Property(property="stok_minimal", type="integer", example=5),
 *     @OA\Property(property="stok_aktual", type="integer", example=10),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Stok untuk lab")
 * )
 * @OA\Schema(schema="StoreMasterBarangRequest", type="object", required={"nama_barang","id_kategori","id_merek","id_satuan","jenis_barang","stok_minimal"},
 *     @OA\Property(property="nama_barang", type="string", example="Laptop Lenovo ThinkPad"),
 *     @OA\Property(property="id_kategori", type="integer", example=1),
 *     @OA\Property(property="id_merek", type="integer", example=1),
 *     @OA\Property(property="id_satuan", type="integer", example=1),
 *     @OA\Property(property="jenis_barang", type="string", enum={"Inventaris","Consumable"}, example="Inventaris"),
 *     @OA\Property(property="stok_minimal", type="integer", example=5),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Stok untuk lab")
 * )
 * @OA\Schema(schema="UpdateMasterBarangRequest", type="object",
 *     @OA\Property(property="nama_barang", type="string", example="Laptop Lenovo V14"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Update spek")
 * )
 * @OA\Schema(schema="MasterBarangListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar master barang berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MasterBarangResource"))
 * )
 * @OA\Schema(schema="MasterBarangSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail master barang berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/MasterBarangResource")
 * )
 * @OA\Schema(schema="MasterBarangDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Master barang berhasil dihapus.")
 * )
 */
class MasterBarangController extends Controller
{
    /**
     * @OA\Get(path="/master-barang", operationId="indexMasterBarang", tags={"Master Barang"}, summary="Daftar master barang", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MasterBarangListResponse"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $query = MasterBarang::with(['kategori', 'merek', 'satuan']);
        
        if ($search) {
            $query->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function($q) use ($search) {
                      $q->where('nama_kategori', 'like', "%{$search}%");
                  })
                  ->orWhereHas('merek', function($q) use ($search) {
                      $q->where('nama_merek', 'like', "%{$search}%");
                  });
        }

        $barang = $query->paginate(20);
        return MasterBarangResource::collection($barang)->additional([
            'status' => true, 
            'message' => 'Daftar master barang berhasil diambil.'
        ])->response();
    }

    /**
     * @OA\Post(path="/master-barang", operationId="storeMasterBarang", tags={"Master Barang"}, summary="Tambah master barang", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreMasterBarangRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/MasterBarangSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreMasterBarangRequest $request): JsonResponse
    {
        $barang = MasterBarang::create($request->validated());
        return response()->json(['status' => true, 'message' => 'Master barang berhasil ditambahkan.', 'data' => new MasterBarangResource($barang)], 201);
    }

    /**
     * @OA\Get(path="/master-barang/{id}", operationId="showMasterBarang", tags={"Master Barang"}, summary="Detail master barang", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MasterBarangSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $barang = MasterBarang::with(['kategori', 'merek', 'satuan'])->find($id);
        if (!$barang) { return response()->json(['status' => false, 'message' => 'Master barang tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail master barang berhasil diambil.', 'data' => new MasterBarangResource($barang)]);
    }

    /**
     * @OA\Put(path="/master-barang/{id}", operationId="updateMasterBarang", tags={"Master Barang"}, summary="Update master barang", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateMasterBarangRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MasterBarangSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(UpdateMasterBarangRequest $request, int $id): JsonResponse
    {
        $barang = MasterBarang::find($id);
        if (!$barang) { return response()->json(['status' => false, 'message' => 'Master barang tidak ditemukan.'], 404); }
        $barang->update($request->validated());
        return response()->json(['status' => true, 'message' => 'Master barang berhasil diperbarui.', 'data' => new MasterBarangResource($barang)]);
    }

    /**
     * @OA\Delete(path="/master-barang/{id}", operationId="destroyMasterBarang", tags={"Master Barang"}, summary="Hapus master barang", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MasterBarangDeleteResponse"))
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $barang = MasterBarang::find($id);
        if (!$barang) { return response()->json(['status' => false, 'message' => 'Master barang tidak ditemukan.'], 404); }
        $barang->delete();
        return response()->json(['status' => true, 'message' => 'Master barang berhasil dihapus.']);
    }
}
