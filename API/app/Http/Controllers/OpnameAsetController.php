<?php

namespace App\Http\Controllers;

use App\Models\OpnameAset;
use App\Http\Requests\StoreOpnameAsetRequest;
use App\Http\Requests\UpdateOpnameAsetRequest;
use App\Http\Resources\OpnameAsetResource;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(schema="OpnameAsetResource", type="object",
 *     description="Representasi data opname aset",
 *     @OA\Property(property="id_opname_aset", type="integer", example=1),
 *     @OA\Property(property="kode_inventaris", type="string", example="INV-2026-001"),
 *     @OA\Property(property="tanggal_opname", type="string", format="date", example="2026-04-27"),
 *     @OA\Property(property="id_kondisi", type="integer", nullable=true, example=1),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="pengadaan", ref="#/components/schemas/PengadaanResource", nullable=true),
 *     @OA\Property(property="kondisi", ref="#/components/schemas/KondisiResource", nullable=true)
 * )
 * @OA\Schema(schema="StoreOpnameAsetRequest", type="object",
 *     required={"kode_inventaris","tanggal_opname"},
 *     @OA\Property(property="kode_inventaris", type="string", example="INV-2026-001"),
 *     @OA\Property(property="tanggal_opname", type="string", format="date", example="2026-04-27"),
 *     @OA\Property(property="id_kondisi", type="integer", nullable=true, example=1),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdateOpnameAsetRequest", type="object",
 *     @OA\Property(property="kode_inventaris", type="string"),
 *     @OA\Property(property="tanggal_opname", type="string", format="date"),
 *     @OA\Property(property="id_kondisi", type="integer", nullable=true),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="OpnameAsetListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar opname aset berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/OpnameAsetResource"))
 * )
 * @OA\Schema(schema="OpnameAsetSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail opname aset berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/OpnameAsetResource")
 * )
 * @OA\Schema(schema="OpnameAsetDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Opname aset berhasil dihapus.")
 * )
 */
class OpnameAsetController extends Controller
{
    /**
     * @OA\Get(path="/opname-aset", operationId="indexOpnameAset", tags={"Opname Aset"}, summary="Daftar opname aset", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/OpnameAsetListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = OpnameAset::with(['aset', 'kondisi'])->paginate(20);

        return OpnameAsetResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar opname aset berhasil diambil.',
        ])->response();
    }

    /**
     * @OA\Post(path="/opname-aset", operationId="storeOpnameAset", tags={"Opname Aset"}, summary="Tambah opname aset", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreOpnameAsetRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/OpnameAsetSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreOpnameAsetRequest $request): JsonResponse
    {
        $opname = OpnameAset::create($request->validated());
        $opname->load(['aset', 'kondisi']);

        return response()->json([
            'status'  => true,
            'message' => 'Opname aset berhasil ditambahkan.',
            'data'    => $opname,
        ], 201);
    }

    /**
     * @OA\Get(path="/opname-aset/{id}", operationId="showOpnameAset", tags={"Opname Aset"}, summary="Detail opname aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/OpnameAsetSingleResponse")),
     *     @OA\Response(response=404, description="Opname aset tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $opname = OpnameAset::with(['aset', 'kondisi'])->find($id);

        if (!$opname) {
            return response()->json(['status' => false, 'message' => 'Opname aset tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail opname aset berhasil diambil.',
            'data'    => $opname,
        ]);
    }

    /**
     * @OA\Put(path="/opname-aset/{id}", operationId="updateOpnameAset", tags={"Opname Aset"}, summary="Update opname aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateOpnameAsetRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/OpnameAsetSingleResponse")),
     *     @OA\Response(response=404, description="Opname aset tidak ditemukan")
     * )
     */
    public function update(UpdateOpnameAsetRequest $request, int $id): JsonResponse
    {
        $opname = OpnameAset::find($id);

        if (!$opname) {
            return response()->json(['status' => false, 'message' => 'Opname aset tidak ditemukan.'], 404);
        }

        $opname->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Opname aset berhasil diperbarui.',
            'data'    => $opname->fresh(['aset', 'kondisi']),
        ]);
    }

    /**
     * @OA\Delete(path="/opname-aset/{id}", operationId="destroyOpnameAset", tags={"Opname Aset"}, summary="Hapus opname aset", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/OpnameAsetDeleteResponse")),
     *     @OA\Response(response=404, description="Opname aset tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $opname = OpnameAset::find($id);

        if (!$opname) {
            return response()->json(['status' => false, 'message' => 'Opname aset tidak ditemukan.'], 404);
        }

        $opname->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Opname aset berhasil dihapus.',
        ]);
    }
}
