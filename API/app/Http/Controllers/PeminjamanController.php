<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Http\Requests\StorePeminjamanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(schema="PeminjamanResource", type="object",
 *     description="Representasi data peminjaman",
 *     @OA\Property(property="nomor_peminjaman", type="string", example="PJM-2026-001"),
 *     @OA\Property(property="tanggal_pinjam", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="nama_peminjam", type="string", example="Budi Santoso"),
 *     @OA\Property(property="nomor_telepon", type="string", nullable=true, example="081234567890"),
 *     @OA\Property(property="lama_pinjam_hari", type="integer", example=3),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="status_peminjaman", type="string", example="Sedang Dipinjam"),
 *     @OA\Property(property="detail_peminjaman", type="array", nullable=true, @OA\Items(ref="#/components/schemas/DetailPeminjamanResource"))
 * )
 * @OA\Schema(schema="StorePeminjamanRequest", type="object",
 *     required={"nomor_peminjaman","tanggal_pinjam","nama_peminjam","lama_pinjam_hari","detail"},
 *     @OA\Property(property="nomor_peminjaman", type="string", example="PJM-2026-003"),
 *     @OA\Property(property="tanggal_pinjam", type="string", format="date", example="2026-04-16"),
 *     @OA\Property(property="nama_peminjam", type="string", example="Budi Santoso"),
 *     @OA\Property(property="nomor_telepon", type="string", nullable=true),
 *     @OA\Property(property="lama_pinjam_hari", type="integer", example=5),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="detail", type="array", @OA\Items(type="object",
 *         @OA\Property(property="kode_barang", type="string", example="ASET-DUMMY-1")
 *     ))
 * )
 * @OA\Schema(schema="PeminjamanListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar peminjaman berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PeminjamanResource"))
 * )
 * @OA\Schema(schema="PeminjamanSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail peminjaman berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PeminjamanResource")
 * )
 * @OA\Schema(schema="PeminjamanDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Peminjaman berhasil dihapus.")
 * )
 */
class PeminjamanController extends Controller
{
    /**
     * @OA\Get(path="/peminjaman", operationId="indexPeminjaman", tags={"Peminjaman"}, summary="Daftar peminjaman", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeminjamanListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Peminjaman::with(['detailPeminjaman.aset'])->get();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar peminjaman berhasil diambil.',
            'data'    => $data,
        ]);
    }

    /**
     * @OA\Post(path="/peminjaman", operationId="storePeminjaman", tags={"Peminjaman"}, summary="Buat peminjaman baru", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePeminjamanRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PeminjamanSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        $result = DB::transaction(function () use ($request) {
            $peminjaman = Peminjaman::create([
                'nomor_peminjaman'  => $request->nomor_peminjaman,
                'tanggal_pinjam'    => $request->tanggal_pinjam,
                'nama_peminjam'     => $request->nama_peminjam,
                'nomor_telepon'     => $request->nomor_telepon,
                'lama_pinjam_hari'  => $request->lama_pinjam_hari,
                'keterangan'        => $request->keterangan,
                'status_peminjaman' => 'Sedang Dipinjam',
            ]);

            foreach ($request->detail as $item) {
                DetailPeminjaman::create([
                    'nomor_peminjaman' => $peminjaman->nomor_peminjaman,
                    'kode_barang'      => $item['kode_barang'],
                ]);
            }

            return $peminjaman->load(['detailPeminjaman.aset']);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Peminjaman berhasil disimpan.',
            'data'    => $result,
        ], 201);
    }

    /**
     * @OA\Get(path="/peminjaman/{id}", operationId="showPeminjaman", tags={"Peminjaman"}, summary="Detail peminjaman", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="PJM-2026-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeminjamanSingleResponse")),
     *     @OA\Response(response=404, description="Peminjaman tidak ditemukan")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $peminjaman = Peminjaman::with(['detailPeminjaman.aset'])->find($id);

        if (!$peminjaman) {
            return response()->json(['status' => false, 'message' => 'Peminjaman tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail peminjaman berhasil diambil.',
            'data'    => $peminjaman,
        ]);
    }

    /**
     * @OA\Put(path="/peminjaman/{id}/kembalikan", operationId="kembalikanPeminjaman", tags={"Peminjaman"}, summary="Proses pengembalian", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="PJM-2026-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeminjamanSingleResponse")),
     *     @OA\Response(response=404, description="Peminjaman tidak ditemukan"),
     *     @OA\Response(response=422, description="Peminjaman sudah dikembalikan")
     * )
     */
    public function kembalikan(string $id): JsonResponse
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['status' => false, 'message' => 'Peminjaman tidak ditemukan.'], 404);
        }

        if ($peminjaman->status_peminjaman === 'Dikembalikan') {
            return response()->json(['status' => false, 'message' => 'Peminjaman ini sudah dikembalikan sebelumnya.'], 422);
        }

        $peminjaman->update(['status_peminjaman' => 'Dikembalikan']);

        return response()->json([
            'status'  => true,
            'message' => 'Peminjaman berhasil dikembalikan.',
            'data'    => $peminjaman,
        ]);
    }

    /**
     * @OA\Delete(path="/peminjaman/{id}", operationId="destroyPeminjaman", tags={"Peminjaman"}, summary="Hapus peminjaman", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", example="PJM-2026-001")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PeminjamanDeleteResponse")),
     *     @OA\Response(response=404, description="Peminjaman tidak ditemukan")
     * )
     */
    // public function destroy(string $id): JsonResponse
    // {
    //     $peminjaman = Peminjaman::find($id);

    //     if (!$peminjaman) {
    //         return response()->json(['status' => false, 'message' => 'Peminjaman tidak ditemukan.'], 404);
    //     }

    //     DB::transaction(function () use ($peminjaman) {
    //         $peminjaman->detailPeminjaman()->delete();
    //         $peminjaman->delete();
    //     });

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Peminjaman berhasil dihapus.',
    //     ]);
    // }
}
