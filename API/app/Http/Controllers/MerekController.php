<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMerekRequest;
use App\Http\Requests\UpdateMerekRequest;
use App\Http\Resources\MerekResource;
use App\Models\Merek;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="MerekResource", type="object",
 *     @OA\Property(property="id_merek", type="integer", example=1),
 *     @OA\Property(property="nama_merek", type="string", example="Lenovo"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Merek Laptop")
 * )
 * @OA\Schema(schema="StoreMerekRequest", type="object", required={"nama_merek"},
 *     @OA\Property(property="nama_merek", type="string", example="Lenovo"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Merek Laptop")
 * )
 * @OA\Schema(schema="UpdateMerekRequest", type="object",
 *     @OA\Property(property="nama_merek", type="string", example="Lenovo Updated"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Updated info")
 * )
 * @OA\Schema(schema="MerekListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar merek berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MerekResource"))
 * )
 * @OA\Schema(schema="MerekSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail merek berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/MerekResource")
 * )
 * @OA\Schema(schema="MerekDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Merek berhasil dihapus.")
 * )
 */
class MerekController extends Controller
{
    /**
     * @OA\Get(path="/merek", operationId="indexMerek", tags={"Merek"}, summary="Daftar merek", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MerekListResponse"))
     * )
     */
    public function index(): JsonResponse
    {
        $merek = Merek::all();
        return response()->json(['status' => true, 'message' => 'Daftar merek berhasil diambil.', 'data' => MerekResource::collection($merek)]);
    }

    /**
     * @OA\Post(path="/merek", operationId="storeMerek", tags={"Merek"}, summary="Tambah merek", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreMerekRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/MerekSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreMerekRequest $request): JsonResponse
    {
        $merek = Merek::create($request->validated());
        return response()->json(['status' => true, 'message' => 'Merek berhasil ditambahkan.', 'data' => new MerekResource($merek)], 201);
    }

    /**
     * @OA\Get(path="/merek/{id}", operationId="showMerek", tags={"Merek"}, summary="Detail merek", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MerekSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $merek = Merek::find($id);
        if (!$merek) { return response()->json(['status' => false, 'message' => 'Merek tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail merek berhasil diambil.', 'data' => new MerekResource($merek)]);
    }

    /**
     * @OA\Put(path="/merek/{id}", operationId="updateMerek", tags={"Merek"}, summary="Update merek", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateMerekRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MerekSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(UpdateMerekRequest $request, int $id): JsonResponse
    {
        $merek = Merek::find($id);
        if (!$merek) { return response()->json(['status' => false, 'message' => 'Merek tidak ditemukan.'], 404); }
        $merek->update($request->validated());
        return response()->json(['status' => true, 'message' => 'Merek berhasil diperbarui.', 'data' => new MerekResource($merek)]);
    }

    /**
     * @OA\Delete(path="/merek/{id}", operationId="destroyMerek", tags={"Merek"}, summary="Hapus merek", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MerekDeleteResponse"))
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $merek = Merek::find($id);
        if (!$merek) { return response()->json(['status' => false, 'message' => 'Merek tidak ditemukan.'], 404); }
        $merek->delete();
        return response()->json(['status' => true, 'message' => 'Merek berhasil dihapus.']);
    }
}
