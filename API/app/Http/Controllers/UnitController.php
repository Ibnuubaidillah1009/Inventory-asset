<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="UnitResource", type="object",
 *     @OA\Property(property="id_unit", type="integer", example=1),
 *     @OA\Property(property="nama_unit", type="string", example="Unit Sarana Prasarana")
 * )
 * @OA\Schema(schema="StoreUnitRequest", type="object", required={"nama_unit"},
 *     @OA\Property(property="nama_unit", type="string", maxLength=100, example="Unit Sarana Prasarana")
 * )
 * @OA\Schema(schema="UpdateUnitRequest", type="object",
 *     @OA\Property(property="nama_unit", type="string", maxLength=100, example="Unit Kurikulum")
 * )
 * @OA\Schema(schema="UnitListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar unit berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UnitResource"))
 * )
 * @OA\Schema(schema="UnitSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail unit berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/UnitResource")
 * )
 * @OA\Schema(schema="UnitDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Unit berhasil dihapus.")
 * )
 */
class UnitController extends Controller
{
    /**
     * @OA\Get(path="/unit", operationId="indexUnit", tags={"Unit"}, summary="Daftar semua unit", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UnitListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => 'Daftar unit berhasil diambil.',
            'data'    => UnitResource::collection(Unit::all()),
        ]);
    }

    /**
     * @OA\Post(path="/unit", operationId="storeUnit", tags={"Unit"}, summary="Tambah unit baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreUnitRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/UnitSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreUnitRequest $request): JsonResponse
    {
        $unit = Unit::create($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'Unit berhasil ditambahkan.',
            'data'    => new UnitResource($unit),
        ], 201);
    }

    /**
     * @OA\Get(path="/unit/{id}", operationId="showUnit", tags={"Unit"}, summary="Detail unit", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UnitSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json(['status' => false, 'message' => 'Unit tidak ditemukan.'], 404);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Detail unit berhasil diambil.',
            'data'    => new UnitResource($unit),
        ]);
    }

    /**
     * @OA\Put(path="/unit/{id}", operationId="updateUnit", tags={"Unit"}, summary="Update unit", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateUnitRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UnitSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateUnitRequest $request, string $id): JsonResponse
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json(['status' => false, 'message' => 'Unit tidak ditemukan.'], 404);
        }
        $unit->update($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'Unit berhasil diperbarui.',
            'data'    => new UnitResource($unit),
        ]);
    }

    /**
     * @OA\Delete(path="/unit/{id}", operationId="destroyUnit", tags={"Unit"}, summary="Hapus unit", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UnitDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json(['status' => false, 'message' => 'Unit tidak ditemukan.'], 404);
        }
        $unit->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Unit berhasil dihapus.',
        ]);
    }
}
