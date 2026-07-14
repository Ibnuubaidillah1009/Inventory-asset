<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenggunaRequest;
use App\Http\Requests\UpdatePenggunaRequest;
use App\Http\Resources\PenggunaResource;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(schema="PenggunaResource", type="object",
 *     description="Representasi data pengguna",
 *     @OA\Property(property="id_pengguna", type="integer", example=1),
 *     @OA\Property(property="username", type="string", example="admin"),
 *     @OA\Property(property="id_peran", type="integer", nullable=true, example=1),
 *     @OA\Property(property="peran", ref="#/components/schemas/PeranResource", nullable=true),
 *     @OA\Property(property="id_kelas", type="integer", nullable=true, example=null),
 *     @OA\Property(property="kelas", ref="#/components/schemas/KelasResource", nullable=true),
 *     @OA\Property(property="id_mapel", type="integer", nullable=true, example=null),
 *     @OA\Property(property="mapel", ref="#/components/schemas/MapelResource", nullable=true),
 *     @OA\Property(property="id_unit", type="integer", nullable=true, example=null),
 *     @OA\Property(property="unit", ref="#/components/schemas/UnitResource", nullable=true)
 * )
 * @OA\Schema(schema="StorePenggunaRequest", type="object", required={"username","password","id_peran"},
 *     @OA\Property(property="username", type="string", maxLength=100, example="guru01"),
 *     @OA\Property(property="password", type="string", minLength=8, example="password123"),
 *     @OA\Property(property="id_peran", type="integer", example=2),
 *     @OA\Property(property="id_kelas", type="integer", nullable=true, example=1),
 *     @OA\Property(property="id_mapel", type="integer", nullable=true, example=3),
 *     @OA\Property(property="id_unit", type="integer", nullable=true, example=1)
 * )
 * @OA\Schema(schema="UpdatePenggunaRequest", type="object",
 *     @OA\Property(property="username", type="string", maxLength=100, example="guru01_updated"),
 *     @OA\Property(property="password", type="string", nullable=true, minLength=8, example="newpassword123"),
 *     @OA\Property(property="id_peran", type="integer", example=2),
 *     @OA\Property(property="id_kelas", type="integer", nullable=true, example=1),
 *     @OA\Property(property="id_mapel", type="integer", nullable=true, example=3),
 *     @OA\Property(property="id_unit", type="integer", nullable=true, example=1)
 * )
 * @OA\Schema(schema="PenggunaListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar pengguna berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PenggunaResource"))
 * )
 * @OA\Schema(schema="PenggunaSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail pengguna berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PenggunaResource")
 * )
 * @OA\Schema(schema="PenggunaDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Pengguna berhasil dihapus.")
 * )
 */
class PenggunaController extends Controller
{
    /**
     * Tampilkan daftar semua pengguna.
     *
     * @OA\Get(path="/pengguna", operationId="indexPengguna", tags={"Pengguna"}, summary="Daftar semua pengguna", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PenggunaListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $query = Pengguna::with(['peran', 'kelas', 'mapel', 'unit']);
        
        if ($search) {
            $query->where('username', 'like', "%{$search}%")
                  ->orWhereHas('peran', function($q) use ($search) {
                      $q->where('nama_peran', 'like', "%{$search}%");
                  });
        }

        $pengguna = $query->paginate(20);
        return PenggunaResource::collection($pengguna)->additional([
            'status' => true, 
            'message' => 'Daftar pengguna berhasil diambil.'
        ])->response();
    }

    /**
     * Simpan pengguna baru.
     *
     * @OA\Post(path="/pengguna", operationId="storePengguna", tags={"Pengguna"}, summary="Tambah pengguna baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePenggunaRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PenggunaSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePenggunaRequest $request): JsonResponse
    {
        $pengguna = Pengguna::create($request->validated());
        $pengguna->load(['peran', 'kelas', 'mapel', 'unit']);
        return response()->json(['status' => true, 'message' => 'Pengguna berhasil ditambahkan.', 'data' => new PenggunaResource($pengguna)], 201);
    }

    /**
     * Tampilkan detail satu pengguna.
     *
     * @OA\Get(path="/pengguna/{id}", operationId="showPengguna", tags={"Pengguna"}, summary="Detail pengguna", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PenggunaSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $pengguna = Pengguna::with(['peran.aksesList', 'kelas.rombel.jurusan', 'mapel', 'unit'])->find($id);
        if (!$pengguna) { return response()->json(['status' => false, 'message' => 'Pengguna tidak ditemukan.'], 404); }
        return response()->json(['status' => true, 'message' => 'Detail pengguna berhasil diambil.', 'data' => new PenggunaResource($pengguna)]);
    }

    /**
     * Update data pengguna.
     *
     * @OA\Put(path="/pengguna/{id}", operationId="updatePengguna", tags={"Pengguna"}, summary="Update pengguna", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePenggunaRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PenggunaSingleResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdatePenggunaRequest $request, string $id): JsonResponse
    {
        $pengguna = Pengguna::find($id);
        if (!$pengguna) { return response()->json(['status' => false, 'message' => 'Pengguna tidak ditemukan.'], 404); }
        $data = $request->validated();
        // Jika password tidak diisi, jangan update password
        if (empty($data['password'])) { unset($data['password']); }
        $pengguna->update($data);
        $pengguna->load(['peran', 'kelas', 'mapel', 'unit']);
        return response()->json(['status' => true, 'message' => 'Pengguna berhasil diperbarui.', 'data' => new PenggunaResource($pengguna)]);
    }

    /**
     * Hapus pengguna.
     *
     * @OA\Delete(path="/pengguna/{id}", operationId="destroyPengguna", tags={"Pengguna"}, summary="Hapus pengguna", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PenggunaDeleteResponse")),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $pengguna = Pengguna::find($id);
        if (!$pengguna) { return response()->json(['status' => false, 'message' => 'Pengguna tidak ditemukan.'], 404); }
        $pengguna->delete();
        return response()->json(['status' => true, 'message' => 'Pengguna berhasil dihapus.']);
    }
}
