<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRombelRequest;
use App\Http\Requests\UpdateRombelRequest;
use App\Http\Resources\RombelResource;
use App\Models\Rombel;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="RombelResource", type="object",
 *     @OA\Property(property="id_rombel", type="integer", example=1),
 *     @OA\Property(property="nama_rombel", type="string", example="X TKJ 1"),
 *     @OA\Property(property="id_jurusan", type="integer", example=1),
 *     @OA\Property(property="jurusan", ref="#/components/schemas/JurusanResource", nullable=true),
 *     @OA\Property(property="kelas", type="array", @OA\Items(ref="#/components/schemas/KelasResource"))
 * )
 * @OA\Schema(schema="StoreRombelRequest", type="object", required={"nama_rombel","id_jurusan"},
 *     @OA\Property(property="nama_rombel", type="string", maxLength=100, example="X TKJ 1"),
 *     @OA\Property(property="id_jurusan", type="integer", example=1)
 * )
 * @OA\Schema(schema="UpdateRombelRequest", type="object",
 *     @OA\Property(property="nama_rombel", type="string", maxLength=100, example="XI TKJ 2"),
 *     @OA\Property(property="id_jurusan", type="integer", example=1)
 * )
 * @OA\Schema(schema="RombelListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar rombel berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/RombelResource"))
 * )
 * @OA\Schema(schema="RombelSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail rombel berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/RombelResource")
 * )
 * @OA\Schema(schema="RombelDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Rombel berhasil dihapus.")
 * )
 */
class RombelController extends Controller
{
    /**
     * @OA\Get(path="/rombel", operationId="indexRombel", tags={"Rombel"}, summary="Daftar semua rombel", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RombelListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Rombel::with(['jurusan', 'kelas'])->paginate(20);
        return RombelResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar rombel berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/rombel", operationId="storeRombel", tags={"Rombel"}, summary="Tambah rombel baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreRombelRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/RombelSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreRombelRequest $request): JsonResponse
    {
        $rombel = Rombel::create($request->validated());
        $rombel->load('jurusan');
        return response()->json(['status' => true, 'message' => 'Rombel berhasil ditambahkan.', 'data' => new RombelResource($rombel)], 201);
    }

    /**
     * @OA\Get(path="/rombel/{id}", operationId="showRombel", tags={"Rombel"}, summary="Detail rombel", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RombelSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $rombel = Rombel::with(['jurusan', 'kelas'])->find($id);
        if (!$rombel) { return response()->json(['status' => false, 'message' => 'Rombel tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail rombel berhasil diambil.', 'data' => new RombelResource($rombel)]);
    }

    /**
     * @OA\Put(path="/rombel/{id}", operationId="updateRombel", tags={"Rombel"}, summary="Update rombel", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateRombelRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RombelSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateRombelRequest $request, string $id): JsonResponse
    {
        $rombel = Rombel::find($id);
        if (!$rombel) { return response()->json(['status' => false, 'message' => 'Rombel tidak ditemukan.'], 404); }
        $rombel->update($request->validated());
        $rombel->load('jurusan');
        return response()->json(['status' => true, 'message' => 'Rombel berhasil diperbarui.', 'data' => new RombelResource($rombel)]);
    }

    /**
     * @OA\Delete(path="/rombel/{id}", operationId="destroyRombel", tags={"Rombel"}, summary="Hapus rombel", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/RombelDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $rombel = Rombel::find($id);
        if (!$rombel) { return response()->json(['status' => false, 'message' => 'Rombel tidak ditemukan.'], 404); }
        $rombel->delete();
        return response()->json(['status' => true, 'message' => 'Rombel berhasil dihapus.']);
    }
}
