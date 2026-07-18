<?php

namespace App\Http\Controllers;

use App\Models\PengadaanHabisPakai;
use App\Http\Requests\StorePengadaanHabisPakaiRequest;
use App\Http\Resources\PengadaanHabisPakaiResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="PengadaanHabisPakaiResource", type="object",
 *     description="Representasi data pengadaan habis pakai",
 *     @OA\Property(property="kode_inventaris", type="string", example="INVHP-001"),
 *     @OA\Property(property="tanggal_pengadaan", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="kode_barang", type="string", example="HP-001"),
 *     @OA\Property(property="jumlah", type="integer", example=10),
 *     @OA\Property(property="harga_satuan", type="number", example=50000),
 *     @OA\Property(property="id_pemasok", type="integer", nullable=true, example=1),
 *     @OA\Property(property="kode_gudang", type="string", nullable=true, example="GDG-001"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Pembelian rutin"),
 *     @OA\Property(property="aset_habis_pakai", ref="#/components/schemas/AsetHabisPakaiResource", nullable=true),
 *     @OA\Property(property="pemasok", ref="#/components/schemas/PemasokResource", nullable=true),
 *     @OA\Property(property="gudang", ref="#/components/schemas/GudangResource", nullable=true)
 * )
 * @OA\Schema(schema="StorePengadaanHabisPakaiRequest", type="object",
 *     required={"kode_inventaris","tanggal_pengadaan","kode_barang","jumlah","harga_satuan"},
 *     @OA\Property(property="kode_inventaris", type="string", example="INVHP-001"),
 *     @OA\Property(property="tanggal_pengadaan", type="string", format="date", example="2026-05-01"),
 *     @OA\Property(property="kode_barang", type="string", example="HP-001"),
 *     @OA\Property(property="jumlah", type="integer", example=10),
 *     @OA\Property(property="harga_satuan", type="number", example=50000),
 *     @OA\Property(property="id_pemasok", type="integer", nullable=true, example=1),
 *     @OA\Property(property="kode_gudang", type="string", nullable=true, example="GDG-001"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdatePengadaanHabisPakaiRequest", type="object",
 *     @OA\Property(property="tanggal_pengadaan", type="string", format="date"),
 *     @OA\Property(property="kode_barang", type="string"),
 *     @OA\Property(property="jumlah", type="integer"),
 *     @OA\Property(property="harga_satuan", type="number"),
 *     @OA\Property(property="id_pemasok", type="integer", nullable=true),
 *     @OA\Property(property="kode_gudang", type="string", nullable=true),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="PengadaanHabisPakaiListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar pengadaan habis pakai berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PengadaanHabisPakaiResource"))
 * )
 * @OA\Schema(schema="PengadaanHabisPakaiSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail pengadaan habis pakai berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PengadaanHabisPakaiResource")
 * )
 * @OA\Schema(schema="PengadaanHabisPakaiDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Pengadaan habis pakai berhasil dihapus.")
 * )
 */
class PengadaanHabisPakaiController extends Controller
{
    /**
     * @OA\Get(path="/pengadaan-habis-pakai", operationId="indexPengadaanHabisPakai", tags={"Pengadaan Habis Pakai"}, summary="Daftar pengadaan habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanHabisPakaiListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = PengadaanHabisPakai::with(['asetHabisPakai', 'pemasok', 'gudang'])->paginate(20);
        return PengadaanHabisPakaiResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar pengadaan habis pakai berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/pengadaan-habis-pakai", operationId="storePengadaanHabisPakai", tags={"Pengadaan Habis Pakai"}, summary="Tambah pengadaan habis pakai", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePengadaanHabisPakaiRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PengadaanHabisPakaiSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePengadaanHabisPakaiRequest $request): JsonResponse
    {
        $item = PengadaanHabisPakai::create($request->validated());
        $item->load(['asetHabisPakai', 'pemasok', 'gudang']);
        return response()->json(['status' => true, 'message' => 'Pengadaan habis pakai berhasil ditambahkan.', 'data' => new PengadaanHabisPakaiResource($item)], 201);
    }

    /**
     * @OA\Get(path="/pengadaan-habis-pakai/{id}", operationId="showPengadaanHabisPakai", tags={"Pengadaan Habis Pakai"}, summary="Detail pengadaan habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="INVHP-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanHabisPakaiSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $item = PengadaanHabisPakai::with(['asetHabisPakai', 'pemasok', 'gudang'])->find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Pengadaan habis pakai tidak ditemukan.'], 404);
        return response()->json(['status' => true, 'message' => 'Detail pengadaan habis pakai berhasil diambil.', 'data' => new PengadaanHabisPakaiResource($item)]);
    }

    /**
     * @OA\Put(path="/pengadaan-habis-pakai/{id}", operationId="updatePengadaanHabisPakai", tags={"Pengadaan Habis Pakai"}, summary="Update pengadaan habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="INVHP-001")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePengadaanHabisPakaiRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanHabisPakaiSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $item = PengadaanHabisPakai::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Pengadaan habis pakai tidak ditemukan.'], 404);

        $validated = $request->validate([
            'tanggal_pengadaan' => 'sometimes|date',
            'kode_barang'       => 'sometimes|string|max:50|exists:aset_habis_pakai,kode_barang',
            'jumlah'            => 'nullable|integer|min:1',
            'harga_satuan'      => 'nullable|numeric|min:0',
            'id_pemasok'        => 'nullable|integer|exists:pemasok,id_pemasok',
            'kode_gudang'       => 'nullable|string|max:20|exists:gudang,kode_gudang',
            'keterangan'        => 'nullable|string',
        ]);

        $item->update($validated);
        return response()->json(['status' => true, 'message' => 'Pengadaan habis pakai berhasil diperbarui.', 'data' => new PengadaanHabisPakaiResource($item->fresh(['asetHabisPakai', 'pemasok', 'gudang']))]);
    }

    /**
     * @OA\Delete(path="/pengadaan-habis-pakai/{id}", operationId="destroyPengadaanHabisPakai", tags={"Pengadaan Habis Pakai"}, summary="Hapus pengadaan habis pakai", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="INVHP-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanHabisPakaiDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $item = PengadaanHabisPakai::find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Pengadaan habis pakai tidak ditemukan.'], 404);
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Pengadaan habis pakai berhasil dihapus.']);
    }
}
