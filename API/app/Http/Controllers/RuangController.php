<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRuangRequest;
use App\Http\Requests\UpdateRuangRequest;
use App\Http\Resources\RuangResource;
use App\Models\Ruang;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="RuangResource", type="object",
 *     @OA\Property(property="id_ruang", type="integer", example=1),
 *     @OA\Property(property="nama_ruang", type="string", example="Lab Komputer 1"),
 *     @OA\Property(property="id_lokasi", type="integer", example=1),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Ruangan ber-AC"),
 *     @OA\Property(property="lokasi", ref="#/components/schemas/LokasiResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreRuangRequest", type="object", required={"nama_ruang","id_lokasi"},
 *     @OA\Property(property="nama_ruang", type="string", maxLength=100, example="Lab Komputer 1"),
 *     @OA\Property(property="id_lokasi", type="integer", example=1),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Ruangan ber-AC")
 * )
 * @OA\Schema(schema="UpdateRuangRequest", type="object",
 *     @OA\Property(property="nama_ruang", type="string", maxLength=100, example="Lab Komputer 2"),
 *     @OA\Property(property="id_lokasi", type="integer", example=1),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Ruangan ber-AC")
 * )
 * @OA\Schema(schema="RuangListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar ruang berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/RuangResource"))
 * )
 * @OA\Schema(schema="RuangSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail ruang berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/RuangResource")
 * )
 * @OA\Schema(schema="RuangDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Ruang berhasil dihapus.")
 * )
 */
class RuangController extends Controller
{
    /**
     * @OA\Get(path="/ruang", operationId="indexRuang", tags={"Ruang"}, summary="Daftar semua ruang", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RuangListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Ruang::with('lokasi')->get();
        return response()->json(['status' => true, 'message' => 'Daftar ruang berhasil diambil.', 'data' => RuangResource::collection($data)]);
    }

    /**
     * @OA\Post(path="/ruang", operationId="storeRuang", tags={"Ruang"}, summary="Tambah ruang baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreRuangRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/RuangSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreRuangRequest $request): JsonResponse
    {
        $ruang = Ruang::create($request->validated());
        $ruang->load('lokasi');
        return response()->json(['status' => true, 'message' => 'Ruang berhasil ditambahkan.', 'data' => new RuangResource($ruang)], 201);
    }

    /**
     * @OA\Get(path="/ruang/{id}", operationId="showRuang", tags={"Ruang"}, summary="Detail ruang", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RuangSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $ruang = Ruang::with('lokasi')->find($id);
        if (!$ruang) { return response()->json(['status' => false, 'message' => 'Ruang tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail ruang berhasil diambil.', 'data' => new RuangResource($ruang)]);
    }

    /**
     * @OA\Put(path="/ruang/{id}", operationId="updateRuang", tags={"Ruang"}, summary="Update ruang", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateRuangRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RuangSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateRuangRequest $request, string $id): JsonResponse
    {
        $ruang = Ruang::find($id);
        if (!$ruang) { return response()->json(['status' => false, 'message' => 'Ruang tidak ditemukan.'], 404); }
        $ruang->update($request->validated());
        $ruang->load('lokasi');
        return response()->json(['status' => true, 'message' => 'Ruang berhasil diperbarui.', 'data' => new RuangResource($ruang)]);
    }

    /**
     * @OA\Delete(path="/ruang/{id}", operationId="destroyRuang", tags={"Ruang"}, summary="Hapus ruang", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RuangDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $ruang = Ruang::find($id);
        if (!$ruang) { return response()->json(['status' => false, 'message' => 'Ruang tidak ditemukan.'], 404); }
        $ruang->delete();
        return response()->json(['status' => true, 'message' => 'Ruang berhasil dihapus.']);
    }
}
