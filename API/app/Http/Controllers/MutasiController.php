<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
// use App\Models\DetailMutasi;
use App\Models\Aset;
use App\Http\Requests\StoreMutasiRequest;
use App\Http\Requests\UpdateMutasiRequest;
use App\Http\Resources\MutasiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * @OA\Schema(schema="MutasiResource", type="object",
 *     description="Representasi data mutasi aset",
 *     @OA\Property(property="id_mutasi", type="integer", example=1),
 *     @OA\Property(property="id_jurusan_asal", type="integer", nullable=true, example=1),
 *     @OA\Property(property="id_jurusan_tujuan", type="integer", example=2),
 *     @OA\Property(property="tanggal_mutasi", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="jurusan_asal", ref="#/components/schemas/JurusanResource", nullable=true),
 *     @OA\Property(property="jurusan_tujuan", ref="#/components/schemas/JurusanResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreMutasiRequest", type="object",
 *     required={"id_jurusan_tujuan","tanggal_mutasi","jumlah_mutasi"},
 *     description="Payload untuk membuat mutasi aset baru",
 *     @OA\Property(property="id_jurusan_asal", type="integer", nullable=true, example=1),
 *     @OA\Property(property="id_jurusan_tujuan", type="integer", example=2),
 *     @OA\Property(property="tanggal_mutasi", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="jumlah_mutasi", type="integer", example=1),
 *     @OA\Property(property="alasan_mutasi", type="string", nullable=true)
 * )
 * @OA\Schema(schema="MutasiListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar mutasi aset berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MutasiResource"))
 * )
 * @OA\Schema(schema="MutasiSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail mutasi berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/MutasiResource")
 * )
 * @OA\Schema(schema="MutasiDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Data mutasi berhasil dihapus.")
 * )
 */
class MutasiController extends Controller
{
    /**
     * @OA\Get(path="/mutasi", operationId="indexMutasi", tags={"Mutasi"}, summary="Daftar mutasi", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MutasiListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Mutasi::with(['jurusanAsal', 'jurusanTujuan'])->paginate(20);

        return MutasiResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar mutasi aset berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/mutasi", operationId="storeMutasi", tags={"Mutasi"}, summary="Buat mutasi aset", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreMutasiRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/MutasiSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal"),
     *     @OA\Response(response=500, description="Terjadi kesalahan pada server")
     * )
     */
    public function store(StoreMutasiRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $newId = Mutasi::max('id_mutasi') + 1;

            $mutasi = Mutasi::create([
                'id_mutasi'         => $newId,
                'id_jurusan_asal'   => $validated['id_jurusan_asal'] ?? null,
                'id_jurusan_tujuan' => $validated['id_jurusan_tujuan'],
                'kode_inventaris'   => $validated['kode_inventaris'],
                'tanggal_mutasi'    => $validated['tanggal_mutasi'],
                'alasan_mutasi'     => $validated['alasan_mutasi'] ?? null,
            ]);

            $mutasi->load(['jurusanAsal', 'jurusanTujuan']);

            return response()->json([
                'status'  => true,
                'message' => 'Mutasi aset berhasil disimpan.',
                'data'    => new MutasiResource($mutasi),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan mutasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(path="/mutasi/{id}", operationId="showMutasi", tags={"Mutasi"}, summary="Detail mutasi", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MutasiSingleResponse")),
     *     @OA\Response(response=404, description="Mutasi tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $mutasi = Mutasi::with(['jurusanAsal', 'jurusanTujuan'])->find($id);

        if (!$mutasi) {
            return response()->json(['status' => false, 'message' => 'Mutasi tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail mutasi berhasil diambil.',
            'data'    => new MutasiResource($mutasi),
        ]);
    }

    /**
     * @OA\Delete(path="/mutasi/{id}", operationId="destroyMutasi", tags={"Mutasi"}, summary="Hapus log mutasi", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MutasiDeleteResponse")),
     *     @OA\Response(response=404, description="Mutasi tidak ditemukan"),
     *     @OA\Response(response=500, description="Terjadi kesalahan pada server")
     * )
     */
    public function update(UpdateMutasiRequest $request, int $id): JsonResponse
    {
        $mutasi = Mutasi::find($id);

        if (!$mutasi) {
            return response()->json(['status' => false, 'message' => 'Mutasi tidak ditemukan.'], 404);
        }

        $validated = $request->validated();

        try {
            $mutasi->update([
                'id_jurusan_asal'   => $validated['id_jurusan_asal'] ?? $mutasi->id_jurusan_asal,
                'id_jurusan_tujuan' => $validated['id_jurusan_tujuan'] ?? $mutasi->id_jurusan_tujuan,
                'kode_inventaris'   => $validated['kode_inventaris'] ?? $mutasi->kode_inventaris,
                'tanggal_mutasi'    => $validated['tanggal_mutasi'] ?? $mutasi->tanggal_mutasi,
                'alasan_mutasi'     => $validated['alasan_mutasi'] ?? $mutasi->alasan_mutasi,
            ]);

            $mutasi->load(['jurusanAsal', 'jurusanTujuan']);

            return response()->json([
                'status'  => true,
                'message' => 'Mutasi berhasil diperbarui.',
                'data'    => new MutasiResource($mutasi),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui mutasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $mutasi = Mutasi::find($id);

        if (!$mutasi) {
            return response()->json(['status' => false, 'message' => 'Mutasi tidak ditemukan.'], 404);
        }

        try {
            $mutasi->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data mutasi berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus mutasi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
