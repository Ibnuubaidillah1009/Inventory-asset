<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengaturanRequest;
use App\Http\Requests\UpdatePengaturanRequest;
use App\Http\Resources\PengaturanResource;
use App\Models\Pengaturan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Pengaturan Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="PengaturanResource",
 *     type="object",
 *     description="Representasi data pengaturan lembaga",
 *     @OA\Property(property="id_pengaturan", type="integer", example=1),
 *     @OA\Property(property="nama_instansi", type="string", example="SMK Negeri 1 Contoh"),
 *     @OA\Property(property="alamat_instansi", type="string", nullable=true, example="Jl. Pendidikan No.1"),
 *     @OA\Property(property="wallpaper_aplikasi", type="string", nullable=true, example="pengaturan/logo.png"),
 *     @OA\Property(property="telpon", type="string", nullable=true, example="021-12345678"),
 *     @OA\Property(property="website", type="string", nullable=true, example="https://smkn1contoh.sch.id"),
 *     @OA\Property(property="email", type="string", nullable=true, example="info@smkn1contoh.sch.id"),
 *     @OA\Property(property="kota", type="string", nullable=true, example="Jakarta"),
 *     @OA\Property(property="kepala_sekolah", type="string", nullable=true, example="Dr. Budi Santoso, M.Pd"),
 *     @OA\Property(property="NIP", type="string", nullable=true, example="196801011990031001"),
 *     @OA\Property(property="bagian_inventaris", type="string", nullable=true, example="Bagian Sarana dan Prasarana")
 * )
 *
 * @OA\Schema(
 *     schema="PengaturanSingleResponse",
 *     type="object",
 *     description="Response wrapper untuk satu pengaturan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Data pengaturan berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PengaturanResource")
 * )
 *
 * @OA\Schema(
 *     schema="PengaturanNullResponse",
 *     type="object",
 *     description="Response wrapper ketika belum ada data pengaturan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Belum ada data pengaturan. Silakan buat pengaturan baru."),
 *     @OA\Property(property="data", type="object", nullable=true, example=null)
 * )
 *
 * @OA\Schema(
 *     schema="PengaturanDeleteResponse",
 *     type="object",
 *     description="Response wrapper untuk penghapusan pengaturan",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Pengaturan berhasil dihapus.")
 * )
 */
class PengaturanController extends Controller
{
    /**
     * @OA\Get(path="/pengaturan", operationId="indexPengaturan", tags={"Pengaturan"}, summary="Ambil data pengaturan lembaga", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Data pengaturan berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/PengaturanSingleResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $pengaturan = Pengaturan::first();
        if (!$pengaturan) {
            return response()->json([
                'status'  => true,
                'message' => 'Belum ada data pengaturan. Silakan buat pengaturan baru.',
                'data'    => null,
            ]);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Data pengaturan berhasil diambil.',
            'data'    => new PengaturanResource($pengaturan),
        ]);
    }

    /**
     * @OA\Post(path="/pengaturan", operationId="storePengaturan", tags={"Pengaturan"}, summary="Buat pengaturan lembaga", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             required={"nama_instansi"},
     *             @OA\Property(property="nama_instansi", type="string"),
     *             @OA\Property(property="alamat_instansi", type="string"),
     *             @OA\Property(property="wallpaper_aplikasi", type="string", format="binary"),
     *             @OA\Property(property="telpon", type="string"),
     *             @OA\Property(property="website", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="kota", type="string"),
     *             @OA\Property(property="kepala_sekolah", type="string"),
     *             @OA\Property(property="NIP", type="string"),
     *             @OA\Property(property="bagian_inventaris", type="string")
     *         )
     *     )),
     *     @OA\Response(response=201, description="Pengaturan lembaga berhasil dibuat",
     *         @OA\JsonContent(ref="#/components/schemas/PengaturanSingleResponse")
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePengaturanRequest $request): JsonResponse
    {

        $data = $request->validated();

        // Handle file upload logo
        if ($request->hasFile('wallpaper_aplikasi')) {
            $path = $request->file('wallpaper_aplikasi')->store('pengaturan', 'public');
            $data['wallpaper_aplikasi'] = $path;
        }

        $pengaturan = Pengaturan::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Pengaturan lembaga berhasil dibuat.',
            'data'    => new PengaturanResource($pengaturan),
        ], 201);
    }

    /**
     * @OA\Get(path="/pengaturan/{id}", operationId="showPengaturan", tags={"Pengaturan"}, summary="Detail pengaturan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Detail pengaturan berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/PengaturanSingleResponse")
     *     ),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $pengaturan = Pengaturan::find($id);
        if (!$pengaturan) {
            return response()->json(['status' => false, 'message' => 'Pengaturan tidak ditemukan.'], 404);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Detail pengaturan berhasil diambil.',
            'data'    => new PengaturanResource($pengaturan),
        ]);
    }

    /**
     * @OA\Post(path="/pengaturan/{id}", operationId="updatePengaturan", tags={"Pengaturan"}, summary="Update pengaturan lembaga", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(property="nama_instansi", type="string"),
     *             @OA\Property(property="alamat_instansi", type="string"),
     *             @OA\Property(property="wallpaper_aplikasi", type="string", format="binary"),
     *             @OA\Property(property="telpon", type="string"),
     *             @OA\Property(property="website", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="kota", type="string"),
     *             @OA\Property(property="kepala_sekolah", type="string"),
     *             @OA\Property(property="NIP", type="string"),
     *             @OA\Property(property="bagian_inventaris", type="string")
     *         )
     *     )),
     *     @OA\Response(response=200, description="Pengaturan lembaga berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/PengaturanSingleResponse")
     *     ),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function update(UpdatePengaturanRequest $request, int $id): JsonResponse
    {
        $pengaturan = Pengaturan::find($id);
        if (!$pengaturan) {
            return response()->json(['status' => false, 'message' => 'Pengaturan tidak ditemukan.'], 404);
        }

        $data = $request->validated();

        // Handle file upload logo
        if ($request->hasFile('wallpaper_aplikasi')) {
            // Hapus logo lama jika ada
            if ($pengaturan->wallpaper_aplikasi) {
                Storage::disk('public')->delete($pengaturan->wallpaper_aplikasi);
            }
            $path = $request->file('wallpaper_aplikasi')->store('pengaturan', 'public');
            $data['wallpaper_aplikasi'] = $path;
        }

        $pengaturan->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Pengaturan lembaga berhasil diperbarui.',
            'data'    => new PengaturanResource($pengaturan),
        ]);
    }

    /**
     * @OA\Delete(path="/pengaturan/{id}", operationId="destroyPengaturan", tags={"Pengaturan"}, summary="Hapus pengaturan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Pengaturan berhasil dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/PengaturanDeleteResponse")
     *     ),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $pengaturan = Pengaturan::find($id);
        if (!$pengaturan) {
            return response()->json(['status' => false, 'message' => 'Pengaturan tidak ditemukan.'], 404);
        }

        // Hapus logo jika ada
        if ($pengaturan->wallpaper_aplikasi) {
            Storage::disk('public')->delete($pengaturan->wallpaper_aplikasi);
        }

        $pengaturan->delete();
        return response()->json(['status' => true, 'message' => 'Pengaturan berhasil dihapus.']);
    }
}
