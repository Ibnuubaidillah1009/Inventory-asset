<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenghapusanAsetRequest;
use App\Http\Resources\PenghapusanAsetResource;
use App\Models\Aset;
use App\Models\PenghapusanAset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(schema="PenghapusanAsetResource", type="object",
 *     @OA\Property(property="id_penghapusan", type="integer", example=1),
 *     @OA\Property(property="kode_barang", type="string", example="ASET-DUMMY-1"),
 *     @OA\Property(property="aset", ref="#/components/schemas/AsetResource", nullable=true),
 *     @OA\Property(property="tanggal_hapus", type="string", format="date", nullable=true, example="2026-04-18"),
 *     @OA\Property(property="alasan_hapus", type="string", nullable=true, example="Sudah tidak layak pakai"),
 *     @OA\Property(property="id_penyetuju", type="integer", nullable=true, example=2),
 *     @OA\Property(property="penyetuju", ref="#/components/schemas/PenggunaResource", nullable=true)
 * )
 * @OA\Schema(schema="StorePenghapusanAsetRequest", type="object", required={"kode_barang","tanggal_hapus","alasan_hapus","id_penyetuju"},
 *     @OA\Property(property="kode_barang", type="string", example="ASET-DUMMY-1"),
 *     @OA\Property(property="tanggal_hapus", type="string", format="date", example="2026-04-18"),
 *     @OA\Property(property="alasan_hapus", type="string", example="Sudah tidak layak pakai"),
 *     @OA\Property(property="id_penyetuju", type="integer", example=2)
 * )
 * @OA\Schema(schema="PenghapusanAsetListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar penghapusan aset berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PenghapusanAsetResource"))
 * )
 * @OA\Schema(schema="PenghapusanAsetSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail penghapusan aset berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PenghapusanAsetResource")
 * )
 * @OA\Schema(schema="PenghapusanAsetDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Data penghapusan aset berhasil dihapus.")
 * )
 */
class PenghapusanAsetController extends Controller
{
    /**
     * @OA\Get(path="/penghapusan-aset", operationId="indexPenghapusanAset", tags={"Penghapusan Aset"}, summary="Daftar semua penghapusan aset", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PenghapusanAsetListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = PenghapusanAset::with(['aset.masterBarang', 'penyetuju'])->orderByDesc('id_penghapusan')->paginate(20);
        return PenghapusanAsetResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar penghapusan aset berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/penghapusan-aset", operationId="storePenghapusanAset", tags={"Penghapusan Aset"}, summary="Simpan penghapusan aset baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePenghapusanAsetRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PenghapusanAsetSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal"),
     *     @OA\Response(response=500, description="Gagal menyimpan")
     * )
     */
    public function store(StorePenghapusanAsetRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $penghapusan = DB::transaction(function () use ($validated) {
                $penghapusan = PenghapusanAset::create([
                    'kode_barang' => $validated['kode_barang'],
                    'tanggal_hapus' => $validated['tanggal_hapus'],
                    'alasan_hapus'  => $validated['alasan_hapus'],
                    'id_penyetuju'  => $validated['id_penyetuju'],
                ]);
                Aset::where('kode_barang', $validated['kode_barang'])->update(['status' => 'Dihapus']);
                return $penghapusan;
            });
            $penghapusan->load(['aset.masterBarang', 'penyetuju']);
            return response()->json(['status' => true, 'message' => 'Penghapusan aset berhasil disimpan.', 'data' => new PenghapusanAsetResource($penghapusan)], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan penghapusan aset.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(path="/penghapusan-aset/{id}", operationId="showPenghapusanAset", tags={"Penghapusan Aset"}, summary="Detail penghapusan aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PenghapusanAsetSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $penghapusan = PenghapusanAset::with(['aset.masterBarang', 'penyetuju'])->find($id);
        if (!$penghapusan) { return response()->json(['status' => false, 'message' => 'Data penghapusan aset tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail penghapusan aset berhasil diambil.', 'data' => new PenghapusanAsetResource($penghapusan)]);
    }

    /**
     * @OA\Delete(path="/penghapusan-aset/{id}", operationId="destroyPenghapusanAset", tags={"Penghapusan Aset"}, summary="Hapus data penghapusan aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PenghapusanAsetDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=500, description="Gagal menghapus")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $penghapusan = PenghapusanAset::find($id);
        if (!$penghapusan) { return response()->json(['status' => false, 'message' => 'Data penghapusan aset tidak ditemukan.'], 404); }
        try {
            $penghapusan->delete();
            return response()->json(['status' => true, 'message' => 'Data penghapusan aset berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal menghapus data penghapusan aset.', 'error' => $e->getMessage()], 500);
        }
    }
}
