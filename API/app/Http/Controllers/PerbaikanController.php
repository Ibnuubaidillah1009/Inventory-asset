<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerbaikanRequest;
use App\Http\Resources\PerbaikanResource;
use App\Models\Aset;
use App\Models\Kerusakan;
use App\Models\Perbaikan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(schema="PerbaikanResource", type="object",
 *     @OA\Property(property="id_perbaikan", type="integer", example=1),
 *     @OA\Property(property="id_kerusakan", type="integer", example=1),
 *     @OA\Property(property="kerusakan", ref="#/components/schemas/KerusakanResource", nullable=true),
 *     @OA\Property(property="tanggal_perbaikan", type="string", format="date", nullable=true, example="2026-04-18"),
 *     @OA\Property(property="teknisi", type="string", nullable=true, example="CV Teknik Jaya"),
 *     @OA\Property(property="biaya_perbaikan", type="number", nullable=true, example=250000),
 *     @OA\Property(property="tindakan_perbaikan", type="string", nullable=true, example="Ganti layar LCD")
 * )
 * @OA\Schema(schema="StorePerbaikanRequest", type="object", required={"id_kerusakan","tanggal_perbaikan","tindakan_perbaikan"},
 *     @OA\Property(property="id_kerusakan", type="integer", example=1),
 *     @OA\Property(property="tanggal_perbaikan", type="string", format="date", example="2026-04-18"),
 *     @OA\Property(property="teknisi", type="string", nullable=true, example="Budi Santoso"),
 *     @OA\Property(property="biaya_perbaikan", type="number", nullable=true, minimum=0, example=250000),
 *     @OA\Property(property="tindakan_perbaikan", type="string", example="Ganti layar LCD")
 * )
 * @OA\Schema(schema="PerbaikanListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar perbaikan berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PerbaikanResource"))
 * )
 * @OA\Schema(schema="PerbaikanSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail perbaikan berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PerbaikanResource")
 * )
 * @OA\Schema(schema="PerbaikanDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Data perbaikan berhasil dihapus.")
 * )
 */
class PerbaikanController extends Controller
{
    /**
     * @OA\Get(path="/perbaikan", operationId="indexPerbaikan", tags={"Perbaikan"}, summary="Daftar semua perbaikan", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PerbaikanListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Perbaikan::with(['kerusakan.aset.masterBarang'])->orderByDesc('id_perbaikan')->paginate(20);
        return PerbaikanResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar perbaikan berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/perbaikan", operationId="storePerbaikan", tags={"Perbaikan"}, summary="Simpan data perbaikan baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePerbaikanRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PerbaikanSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal"),
     *     @OA\Response(response=500, description="Gagal menyimpan")
     * )
     */
    public function store(StorePerbaikanRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $perbaikan = DB::transaction(function () use ($validated) {
                $perbaikan = Perbaikan::create([
                    'id_kerusakan'       => $validated['id_kerusakan'],
                    'tanggal_perbaikan'  => $validated['tanggal_perbaikan'],
                    'teknisi'            => $validated['teknisi'] ?? null,
                    'biaya_perbaikan'    => $validated['biaya_perbaikan'] ?? null,
                    'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
                ]);
                $kerusakan = Kerusakan::findOrFail($validated['id_kerusakan']);
                $kerusakan->update(['status_kerusakan' => 'Sedang Diperbaiki']);
                return $perbaikan;
            });
            $perbaikan->load(['kerusakan.aset.masterBarang']);
            return response()->json(['status' => true, 'message' => 'Data perbaikan berhasil disimpan.', 'data' => new PerbaikanResource($perbaikan)], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan data perbaikan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(path="/perbaikan/{id}", operationId="showPerbaikan", tags={"Perbaikan"}, summary="Detail perbaikan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PerbaikanSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $perbaikan = Perbaikan::with(['kerusakan.aset.masterBarang'])->find($id);
        if (!$perbaikan) { return response()->json(['status' => false, 'message' => 'Perbaikan tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail perbaikan berhasil diambil.', 'data' => new PerbaikanResource($perbaikan)]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $perbaikan = Perbaikan::find($id);
        if (!$perbaikan) { return response()->json(['status' => false, 'message' => 'Perbaikan tidak ditemukan.'], 404); }

        $validated = $request->validate([
            'tanggal_perbaikan' => 'sometimes|date',
            'teknisi' => 'sometimes|nullable|string',
            'biaya_perbaikan' => 'sometimes|nullable|numeric|min:0',
            'tindakan_perbaikan' => 'sometimes|string',
        ]);

        $perbaikan->update($validated);
        $perbaikan->load(['kerusakan.aset.masterBarang']);

        return response()->json(['status' => true, 'message' => 'Perbaikan berhasil diperbarui.', 'data' => new PerbaikanResource($perbaikan)]);
    }

    /**
     * @OA\Delete(path="/perbaikan/{id}", operationId="destroyPerbaikan", tags={"Perbaikan"}, summary="Hapus data perbaikan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PerbaikanDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=500, description="Gagal menghapus")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $perbaikan = Perbaikan::find($id);
        if (!$perbaikan) { return response()->json(['status' => false, 'message' => 'Perbaikan tidak ditemukan.'], 404); }
        try {
            $perbaikan->delete();
            return response()->json(['status' => true, 'message' => 'Data perbaikan berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal menghapus data perbaikan.', 'error' => $e->getMessage()], 500);
        }
    }
}
