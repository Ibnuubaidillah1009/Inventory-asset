<?php

namespace App\Http\Controllers;

use App\Models\Pengadaan;
use App\Models\DetailPengadaan;
use App\Models\Aset;
use App\Http\Requests\StorePengadaanRequest;
use App\Http\Requests\UpdatePengadaanRequest;
use App\Http\Resources\PengadaanResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

/**
 * @OA\Schema(schema="StorePengadaanRequest", type="object",
 *     required={"tanggal_pengadaan", "detail"},
 *     @OA\Property(property="tanggal_pengadaan", type="string", format="date", example="2026-05-16"),
 *     @OA\Property(property="id_pemasok", type="integer", nullable=true, example=1),
 *     @OA\Property(property="total_harga", type="number", nullable=true, example=5000000),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Pengadaan laptop"),
 *     @OA\Property(property="kode_gudang", type="string", nullable=true, example="GDG-1"),
 *     @OA\Property(property="jumlah_pengadaan", type="integer", nullable=true, example=5),
 *     @OA\Property(property="id_kondisi", type="integer", nullable=true, example=1),
 *     @OA\Property(property="sumber_perolehan", type="string", nullable=true, example="APBD"),
 *     @OA\Property(property="permintaan", type="array", @OA\Items(type="string", example="PRM-001")),
 *     @OA\Property(property="detail", type="array", @OA\Items(
 *         @OA\Property(property="id_master_barang", type="integer", example=1),
 *         @OA\Property(property="jumlah_masuk", type="integer", example=5),
 *         @OA\Property(property="harga_satuan", type="number", example=1000000)
 *     ))
 * )
 * @OA\Schema(schema="UpdatePengadaanRequest", type="object",
 *     @OA\Property(property="tanggal_pengadaan", type="string", format="date"),
 *     @OA\Property(property="id_pemasok", type="integer", nullable=true),
 *     @OA\Property(property="total_harga", type="number", nullable=true),
 *     @OA\Property(property="keterangan", type="string", nullable=true),
 *     @OA\Property(property="kode_gudang", type="string", nullable=true),
 *     @OA\Property(property="jumlah_pengadaan", type="integer", nullable=true),
 *     @OA\Property(property="id_kondisi", type="integer", nullable=true),
 *     @OA\Property(property="sumber_perolehan", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", enum={"diproses","dibelanjakan","selesai"}, example="dibelanjakan"),
 *     @OA\Property(property="permintaan", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="detail", type="array", @OA\Items(
 *         @OA\Property(property="id_master_barang", type="integer"),
 *         @OA\Property(property="jumlah_masuk", type="integer"),
 *         @OA\Property(property="harga_satuan", type="number")
 *     ))
 * )
 * @OA\Schema(schema="PengadaanListResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Daftar pengadaan berhasil diambil."),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PengadaanResource"))
 * )
 * @OA\Schema(schema="PengadaanSingleResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Detail pengadaan berhasil diambil."),
 *     @OA\Property(property="data", ref="#/components/schemas/PengadaanResource")
 * )
 * @OA\Schema(schema="PengadaanDeleteResponse", type="object",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Pengadaan berhasil dihapus.")
 * )
 */
class PengadaanController extends Controller
{
    /**
     * @OA\Get(path="/pengadaan", operationId="indexPengadaan", tags={"Pengadaan"}, summary="Daftar pengadaan", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Pengadaan::with(['pemasok', 'gudang', 'kondisi', 'permintaan', 'detailPengadaan.masterBarang'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Daftar pengadaan berhasil diambil.',
            'data' => PengadaanResource::collection($data)
        ]);
    }

    /**
     * @OA\Post(path="/pengadaan", operationId="storePengadaan", tags={"Pengadaan"}, summary="Tambah pengadaan", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePengadaanRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/PengadaanSingleResponse")),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StorePengadaanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Hitung total jumlah jika tidak dikirim
            $jumlah_pengadaan = $validated['jumlah_pengadaan'] ?? collect($validated['detail'])->sum('jumlah_masuk');

            $pengadaan = Pengadaan::create([
                'tanggal_pengadaan' => $validated['tanggal_pengadaan'],
                'id_pemasok'        => $validated['id_pemasok'] ?? null,
                'total_harga'       => $validated['total_harga'] ?? null,
                'keterangan'        => $validated['keterangan'] ?? null,
                'kode_gudang'       => $validated['kode_gudang'] ?? null,
                'jumlah_pengadaan'  => $jumlah_pengadaan,
                'id_kondisi'        => $validated['id_kondisi'] ?? null,
                'sumber_perolehan'  => $validated['sumber_perolehan'] ?? null,
                'status'            => 'diproses',
            ]);

            // Insert Detail Pengadaan
            foreach ($validated['detail'] as $det) {
                DetailPengadaan::create([
                    'id_pengadaan'     => $pengadaan->id_pengadaan,
                    'id_master_barang' => $det['id_master_barang'],
                    'jumlah_masuk'     => $det['jumlah_masuk'],
                    'harga_satuan'     => $det['harga_satuan'] ?? null,
                ]);
            }

            // Insert Pengadaan Permintaan Pivot
            if (!empty($validated['permintaan'])) {
                $pengadaan->permintaan()->sync($validated['permintaan']);
            }

            DB::commit();

            $pengadaan->load(['pemasok', 'gudang', 'kondisi', 'permintaan', 'detailPengadaan.masterBarang']);

            return response()->json([
                'status' => true,
                'message' => 'Pengadaan berhasil ditambahkan.',
                'data' => new PengadaanResource($pengadaan)
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan pengadaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(path="/pengadaan/{id}", operationId="showPengadaan", tags={"Pengadaan"}, summary="Detail pengadaan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanSingleResponse")),
     *     @OA\Response(response=404, description="Pengadaan tidak ditemukan")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $pengadaan = Pengadaan::with(['pemasok', 'gudang', 'kondisi', 'permintaan', 'detailPengadaan.masterBarang', 'aset'])->find($id);
        if (!$pengadaan) return response()->json(['status' => false, 'message' => 'Pengadaan tidak ditemukan.'], 404);
        
        return response()->json([
            'status' => true,
            'message' => 'Detail pengadaan berhasil diambil.',
            'data' => new PengadaanResource($pengadaan)
        ]);
    }

    /**
     * @OA\Put(path="/pengadaan/{id}", operationId="updatePengadaan", tags={"Pengadaan"}, summary="Update pengadaan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePengadaanRequest")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanSingleResponse")),
     *     @OA\Response(response=404, description="Pengadaan tidak ditemukan")
     * )
     */
    public function update(UpdatePengadaanRequest $request, int $id): JsonResponse
    {
        $pengadaan = Pengadaan::find($id);
        if (!$pengadaan) return response()->json(['status' => false, 'message' => 'Pengadaan tidak ditemukan.'], 404);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Update fields yang diijinkan
            $updateData = [];
            foreach (['tanggal_pengadaan', 'id_pemasok', 'total_harga', 'keterangan', 'kode_gudang', 'jumlah_pengadaan', 'id_kondisi', 'sumber_perolehan', 'status'] as $field) {
                if (isset($validated[$field])) {
                    $updateData[$field] = $validated[$field];
                }
            }

            if (!empty($updateData)) {
                $pengadaan->update($updateData);
            }

            // Update details if provided
            if (isset($validated['detail'])) {
                $pengadaan->detailPengadaan()->delete();
                foreach ($validated['detail'] as $det) {
                    DetailPengadaan::create([
                        'id_pengadaan'     => $pengadaan->id_pengadaan,
                        'id_master_barang' => $det['id_master_barang'],
                        'jumlah_masuk'     => $det['jumlah_masuk'],
                        'harga_satuan'     => $det['harga_satuan'] ?? null,
                    ]);
                }
                if (!isset($updateData['jumlah_pengadaan'])) {
                    $pengadaan->update(['jumlah_pengadaan' => collect($validated['detail'])->sum('jumlah_masuk')]);
                }
            }

            // Update pivot if provided
            if (isset($validated['permintaan'])) {
                $pengadaan->permintaan()->sync($validated['permintaan']);
            }

            // Generate Aset jika status dibelanjakan
            if (isset($validated['status']) && $validated['status'] === 'dibelanjakan') {
                $this->generateAset($pengadaan);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pengadaan berhasil diperbarui.',
                'data' => new PengadaanResource($pengadaan->fresh(['pemasok', 'gudang', 'kondisi', 'permintaan', 'detailPengadaan.masterBarang', 'aset']))
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui pengadaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(path="/pengadaan/{id}", operationId="destroyPengadaan", tags={"Pengadaan"}, summary="Hapus pengadaan", security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PengadaanDeleteResponse")),
     *     @OA\Response(response=404, description="Pengadaan tidak ditemukan")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $pengadaan = Pengadaan::find($id);
        if (!$pengadaan) return response()->json(['status' => false, 'message' => 'Pengadaan tidak ditemukan.'], 404);
        
        DB::beginTransaction();
        try {
            $pengadaan->permintaan()->detach();
            $pengadaan->detailPengadaan()->delete();
            $pengadaan->aset()->delete(); // Hapus aset jika pengadaan dihapus
            $pengadaan->delete();
            
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Pengadaan berhasil dihapus.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus pengadaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Aset secara otomatis berdasarkan Detail Pengadaan.
     * Dipanggil ketika status pengadaan berubah menjadi 'dibelanjakan'.
     */
    private function generateAset(Pengadaan $pengadaan): void
    {
        // Cek apakah aset sudah pernah di-generate untuk pengadaan ini
        $asetExists = Aset::where('id_pengadaan', $pengadaan->id_pengadaan)->exists();
        if ($asetExists) {
            return; // Jangan generate ulang
        }

        $details = DetailPengadaan::where('id_pengadaan', $pengadaan->id_pengadaan)->get();
        
        foreach ($details as $detail) {
            for ($i = 0; $i < $detail->jumlah_masuk; $i++) {
                $kodeBarang = 'ASET-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
                
                $maxRetries = 10;
                $attempts = 0;
                while (Aset::where('kode_barang', $kodeBarang)->exists()) {
                    $kodeBarang = 'ASET-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
                    $attempts++;
                    if ($attempts >= $maxRetries) {
                        throw new \Exception("Gagal membuat kode barang unik setelah {$maxRetries} percobaan.");
                    }
                }

                Aset::create([
                    'kode_barang'        => $kodeBarang,
                    'id_pengadaan'       => $pengadaan->id_pengadaan,
                    'id_master_barang'   => $detail->id_master_barang,
                    'tanggal_registrasi' => now()->format('Y-m-d'),
                    'gambar'             => null,
                    'keterangan'         => null,
                    'status'             => 'Di Gudang', // Enum status awal
                ]);
            }
        }
    }
}
