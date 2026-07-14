<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeranRequest;
use App\Http\Requests\UpdatePeranRequest;
use App\Http\Resources\PeranResource;
use App\Models\Peran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(schema="PeranResource", type="object",
 *     @OA\Property(property="id_peran", type="integer", example=1),
 *     @OA\Property(property="nama_peran", type="string", example="Administrator"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Peran administrator sistem"),
 *     @OA\Property(property="akses_list", type="array", @OA\Items(ref="#/components/schemas/AksesResource"))
 * )
 * @OA\Schema(schema="StorePeranRequest", type="object", required={"nama_peran"},
 *     @OA\Property(property="nama_peran", type="string", maxLength=100, example="Kepala Sekolah"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="UpdatePeranRequest", type="object",
 *     @OA\Property(property="nama_peran", type="string", maxLength=100, example="Wakil Kepala Sekolah"),
 *     @OA\Property(property="keterangan", type="string", nullable=true)
 * )
 * @OA\Schema(schema="SyncAksesRequest", type="object", required={"id_akses"},
 *     @OA\Property(property="id_akses", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 * )
 * @OA\Schema(schema="PeranListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar peran berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PeranResource"))
 * )
 * @OA\Schema(schema="PeranSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail peran berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PeranResource")
 * )
 * @OA\Schema(schema="PeranDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Peran berhasil dihapus.")
 * )
 */
class PeranController extends Controller
{
    /**
     * Tampilkan daftar semua peran.
     *
     * @OA\Get(path="/peran", operationId="indexPeran", tags={"Peran"}, summary="Daftar semua peran", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeranListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $peran = Peran::with('aksesList')->paginate(20);
        return response()->json(['status' => true, 'message' => 'Daftar peran berhasil diambil.', 'data' => PeranResource::collection($peran)]);
    }

    /**
     * Simpan peran baru.
     *
     * @OA\Post(path="/peran", operationId="storePeran", tags={"Peran"}, summary="Tambah peran baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePeranRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PeranSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePeranRequest $request): JsonResponse
    {
        $peran = Peran::create($request->validated());
        return response()->json(['status' => true, 'message' => 'Peran berhasil ditambahkan.', 'data' => new PeranResource($peran)], 201);
    }

    /**
     * Tampilkan detail satu peran.
     *
     * @OA\Get(path="/peran/{id}", operationId="showPeran", tags={"Peran"}, summary="Detail peran", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeranSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $peran = Peran::with('aksesList')->find($id);
        if (!$peran) { return response()->json(['status' => false, 'message' => 'Peran tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail peran berhasil diambil.', 'data' => new PeranResource($peran)]);
    }

    /**
     * Update data peran.
     *
     * @OA\Put(path="/peran/{id}", operationId="updatePeran", tags={"Peran"}, summary="Update peran", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePeranRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeranSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdatePeranRequest $request, string $id): JsonResponse
    {
        $peran = Peran::find($id);
        if (!$peran) { return response()->json(['status' => false, 'message' => 'Peran tidak ditemukan.'], 404); }
        $peran->update($request->validated());
        return response()->json(['status' => true, 'message' => 'Peran berhasil diperbarui.', 'data' => new PeranResource($peran)]);
    }

    /**
     * Hapus peran.
     *
     * @OA\Delete(path="/peran/{id}", operationId="destroyPeran", tags={"Peran"}, summary="Hapus peran", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeranDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $peran = Peran::find($id);
        if (!$peran) { return response()->json(['status' => false, 'message' => 'Peran tidak ditemukan.'], 404); }
        $peran->delete();
        return response()->json(['status' => true, 'message' => 'Peran berhasil dihapus.']);
    }

    /**
     * Sinkronisasi hak akses pada sebuah peran.
     * Menerima array id_akses untuk di-sync ke tabel pivot peran_akses.
     *
     * POST /api/peran/{id}/sync-akses
     * Body: { "id_akses": [1, 2, 3] }
     *
     * @OA\Post(path="/peran/{id}/sync-akses", operationId="syncAksesPeran", tags={"Peran"}, summary="Sinkronisasi hak akses peran", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/SyncAksesRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeranSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function syncAkses(Request $request, string $id): JsonResponse
    {
        $peran = Peran::find($id);
        if (!$peran) { return response()->json(['status' => false, 'message' => 'Peran tidak ditemukan.'], 404); }
        $request->validate(['id_akses' => ['required', 'array'], 'id_akses.*' => ['integer', 'exists:akses,id_akses']]);
        $peran->aksesList()->sync($request->id_akses);
        $peran->load('aksesList');
        return response()->json(['status' => true, 'message' => 'Hak akses peran berhasil disinkronisasi.', 'data' => new PeranResource($peran)]);
    }
}
