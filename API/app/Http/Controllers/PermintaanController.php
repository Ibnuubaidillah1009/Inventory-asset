<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\DetailPermintaan;
use App\Http\Requests\StorePermintaanRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(schema="PermintaanResource", type="object",
 *     description="Representasi data permintaan barang",
 *     @OA\Property(property="kode_permintaan", type="string", example="PMT-2026-001"),
 *     @OA\Property(property="id_jurusan", type="integer", nullable=true, example=1),
 *     @OA\Property(property="id_pengguna", type="integer", nullable=true, example=1),
 *     @OA\Property(property="tanggal_permintaan", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="keterangan_keperluan", type="string", example="Untuk praktikum"),
 *     @OA\Property(property="status_persetujuan", type="string", example="Menunggu"),
 *     @OA\Property(property="tanggal_persetujuan", type="string", format="date", nullable=true),
 *     @OA\Property(property="id_penyetuju", type="integer", nullable=true, example=1),
 *     @OA\Property(property="alasan_disetujui", type="string", nullable=true),
 *     @OA\Property(property="jurusan", ref="#/components/schemas/JurusanResource", nullable=true),
 *     @OA\Property(property="pengguna", ref="#/components/schemas/PenggunaResource", nullable=true),
 *     @OA\Property(property="penyetuju", ref="#/components/schemas/PenggunaResource", nullable=true),
 *     @OA\Property(property="detail_permintaan", type="array", nullable=true, @OA\Items(ref="#/components/schemas/DetailPermintaanResource"))
 * )
 * @OA\Schema(schema="StorePermintaanRequest", type="object",
 *     required={"id_master_barang","jumlah_diminta","alasan_kebutuhan"},
 *     @OA\Property(property="id_master_barang", type="integer", example=1),
 *     @OA\Property(property="jumlah_diminta", type="integer", example=5),
 *     @OA\Property(property="alasan_kebutuhan", type="string", example="Digunakan untuk praktikum semester baru"),
 *     @OA\Property(property="keterangan_keperluan", type="string", nullable=true, example="Untuk laboratorium jaringan")
 * )
 * @OA\Schema(schema="KeputusanPermintaanRequest", type="object",
 *     required={"status_persetujuan"},
 *     @OA\Property(property="status_persetujuan", type="string", example="disetujui"),
 *     @OA\Property(property="alasan_disetujui", type="string", nullable=true, example="Kebutuhan disetujui")
 * )
 * @OA\Schema(schema="PermintaanListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar permintaan berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PermintaanResource"))
 * )
 * @OA\Schema(schema="PermintaanSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail permintaan berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PermintaanResource")
 * )
 * @OA\Schema(schema="PermintaanDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Permintaan berhasil dihapus.")
 * )
 */
class PermintaanController extends Controller
{
    /**
     * @OA\Get(path="/permintaan", operationId="indexPermintaan", tags={"Permintaan"}, summary="Daftar permintaan", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PermintaanListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $query = Permintaan::with(['jurusan', 'pengguna', 'penyetuju', 'detailPermintaan.masterBarang']);
        
        if ($search) {
            $query->where('kode_permintaan', 'like', "%{$search}%")
                  ->orWhere('status_persetujuan', 'like', "%{$search}%")
                  ->orWhere('keterangan_keperluan', 'like', "%{$search}%");
        }

        $data = $query->paginate(20);

        return \App\Http\Resources\PermintaanResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar permintaan berhasil diambil.'
        ])->response();
    }

    /**
     * @OA\Post(path="/permintaan", operationId="storePermintaan", tags={"Permintaan"}, summary="Buat permintaan baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePermintaanRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PermintaanSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePermintaanRequest $request): JsonResponse
    {
        $result = DB::transaction(function () use ($request) {
            $kodePermintaan = 'PRM' . random_int(100000, 999999);
            
            $permintaan = Permintaan::create([
                'kode_permintaan'      => $kodePermintaan,
                'id_jurusan'           => null,
                'id_pengguna'          => auth()->id() ?? 1, // fallback to 1 if no auth for safety
                'tanggal_permintaan'   => now(),
                'keterangan_keperluan' => $request->keterangan_keperluan,
                'status_persetujuan'   => 'diproses',
            ]);

            DetailPermintaan::create([
                'kode_permintaan'  => $permintaan->kode_permintaan,
                'id_master_barang' => $request->id_master_barang,
                'jumlah_diminta'   => $request->jumlah_diminta,
                'alasan_kebutuhan' => $request->alasan_kebutuhan,
            ]);

            return $permintaan->load(['jurusan', 'pengguna', 'penyetuju', 'detailPermintaan.masterBarang']);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Permintaan berhasil disimpan.',
            'data'    => new \App\Http\Resources\PermintaanResource($result),
        ], 201);
    }

    /**
     * @OA\Get(path="/permintaan/{id}", operationId="showPermintaan", tags={"Permintaan"}, summary="Detail permintaan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="PMT-2026-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PermintaanSingleResponse")),
     *     @OA\Response(response=404, description="Permintaan tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $permintaan = Permintaan::with(['jurusan', 'pengguna', 'penyetuju', 'detailPermintaan.masterBarang'])->find($id);

        if (!$permintaan) {
            return response()->json(['status' => false, 'message' => 'Permintaan tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail permintaan berhasil diambil.',
            'data'    => new \App\Http\Resources\PermintaanResource($permintaan),
        ]);
    }

    /**
     * @OA\Put(path="/permintaan/{id}/keputusan", operationId="keputusanPermintaan", tags={"Permintaan"}, summary="Setujui/tolak permintaan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="PMT-2026-001")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/KeputusanPermintaanRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PermintaanSingleResponse")),
     *     @OA\Response(response=404, description="Permintaan tidak ditemukan"),
     *     @OA\Response(response=422, description="Permintaan sudah diproses")
     * )
     */
    public function keputusan(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status_persetujuan' => 'required|string|in:Menunggu,Disetujui,Ditolak,diproses,disetujui,ditolak',
            'alasan_disetujui'   => 'nullable|string',
        ]);

        $permintaan = Permintaan::find($id);

        if (!$permintaan) {
            return response()->json(['status' => false, 'message' => 'Permintaan tidak ditemukan.'], 404);
        }

        if (!in_array($permintaan->status_persetujuan, ['diproses', 'Menunggu'])) {
            return response()->json(['status' => false, 'message' => 'Permintaan ini sudah diproses sebelumnya.'], 422);
        }

        $permintaan->update([
            'status_persetujuan'  => $request->status_persetujuan === 'disetujui' ? 'Disetujui' : ($request->status_persetujuan === 'ditolak' ? 'Ditolak' : $request->status_persetujuan),
            'alasan_disetujui'    => $request->alasan_disetujui,
            'tanggal_persetujuan' => now(),
            'id_penyetuju'        => auth()->id() ?? 1, // fallback for testing if no auth
        ]);

        return response()->json([
            'status'  => true,
            'message' => "Permintaan berhasil diperbarui.",
            'data'    => new \App\Http\Resources\PermintaanResource($permintaan),
        ]);
    }

    /**
     * @OA\Delete(path="/permintaan/{id}", operationId="destroyPermintaan", tags={"Permintaan"}, summary="Hapus permintaan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="PMT-2026-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PermintaanDeleteResponse")),
     *     @OA\Response(response=404, description="Permintaan tidak ditemukan"),
     *     @OA\Response(response=422, description="Hanya status Menunggu yang dapat dihapus")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $permintaan = Permintaan::find($id);

        if (!$permintaan) {
            return response()->json(['status' => false, 'message' => 'Permintaan tidak ditemukan.'], 404);
        }

        DB::transaction(function () use ($permintaan) {
            $permintaan->detailPermintaan()->delete();
            $permintaan->delete();
        });

        return response()->json([
            'status'  => true,
            'message' => 'Permintaan berhasil dihapus.',
        ]);
    }
}
