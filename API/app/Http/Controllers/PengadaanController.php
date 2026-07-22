<?php

namespace App\Http\Controllers;

use App\Models\Pengadaan;
use App\Models\DetailPengadaan;
use App\Models\Aset;
use App\Http\Requests\StorePengadaanRequest;
use App\Http\Requests\UpdatePengadaanRequest;
use App\Http\Resources\PengadaanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class PengadaanController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Pengadaan::with(['pemasok', 'gudang', 'kondisi', 'sumberPerolehan', 'permintaan', 'detailPengadaan.masterBarang'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_po', 'like', "%{$search}%")
                      ->orWhere('nomor_faktur', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return PengadaanResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar pengadaan berhasil diambil.',
        ])->response();
    }

    public function store(StorePengadaanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $jumlah_pengadaan = $validated['jumlah_pengadaan'] ?? collect($validated['detail'])->sum('jumlah_masuk');

            $pengadaan = Pengadaan::create([
                'nomor_po'              => $validated['nomor_po'] ?? null,
                'nomor_faktur'          => $validated['nomor_faktur'] ?? null,
                'tanggal_pengadaan'     => $validated['tanggal_pengadaan'],
                'id_pemasok'            => $validated['id_pemasok'] ?? null,
                'total_harga'           => $validated['total_harga'] ?? null,
                'persentase_ppn'        => $validated['persentase_ppn'] ?? null,
                'nominal_ppn'           => $validated['nominal_ppn'] ?? null,
                'grand_total'           => $validated['grand_total'] ?? null,
                'keterangan'            => $validated['keterangan'] ?? null,
                'kode_gudang'           => $validated['kode_gudang'] ?? null,
                'jumlah_pengadaan'      => $jumlah_pengadaan,
                'id_kondisi'            => $validated['id_kondisi'] ?? null,
                'id_sumber_perolehan'   => $validated['id_sumber_perolehan'] ?? null,
                'tanggal_pengiriman'    => $validated['tanggal_pengiriman'] ?? null,
                'nomor_po_lampiran'     => $validated['nomor_po_lampiran'] ?? null,
                'status'                => $validated['status'] ?? 'Menunggu Proses',
            ]);

            if (!empty($validated['detail'])) {
                foreach ($validated['detail'] as $det) {
                    DetailPengadaan::create([
                        'id_pengadaan'     => $pengadaan->id_pengadaan,
                        'id_master_barang' => $det['id_master_barang'],
                        'jumlah_masuk'     => $det['jumlah_masuk'],
                        'harga_satuan'     => $det['harga_satuan'] ?? null,
                    ]);
                }
            }

            if (!empty($validated['permintaan'])) {
                $pengadaan->permintaan()->sync($validated['permintaan']);
            }

            DB::commit();

            $pengadaan->load(['pemasok', 'gudang', 'kondisi', 'sumberPerolehan', 'permintaan', 'detailPengadaan.masterBarang']);

            return response()->json([
                'status'  => true,
                'message' => 'Pengadaan berhasil ditambahkan.',
                'data'    => new PengadaanResource($pengadaan),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan pengadaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $pengadaan = Pengadaan::with(['pemasok', 'gudang', 'kondisi', 'sumberPerolehan', 'permintaan', 'detailPengadaan.masterBarang', 'aset'])->find($id);
        if (!$pengadaan) return response()->json(['status' => false, 'message' => 'Pengadaan tidak ditemukan.'], 404);

        return response()->json([
            'status'  => true,
            'message' => 'Detail pengadaan berhasil diambil.',
            'data'    => new PengadaanResource($pengadaan),
        ]);
    }

    public function update(UpdatePengadaanRequest $request, int $id): JsonResponse
    {
        $pengadaan = Pengadaan::find($id);
        if (!$pengadaan) return response()->json(['status' => false, 'message' => 'Pengadaan tidak ditemukan.'], 404);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $allowedFields = [
                'nomor_po', 'nomor_faktur', 'tanggal_pengadaan', 'id_pemasok',
                'total_harga', 'persentase_ppn', 'nominal_ppn', 'grand_total',
                'keterangan', 'kode_gudang', 'jumlah_pengadaan', 'id_kondisi',
                'id_sumber_perolehan', 'tanggal_pengiriman', 'nomor_po_lampiran', 'status',
            ];

            $updateData = [];
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $updateData[$field] = $validated[$field];
                }
            }

            if (!empty($updateData)) {
                $pengadaan->update($updateData);
            }

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

            if (isset($validated['permintaan'])) {
                $pengadaan->permintaan()->sync($validated['permintaan']);
            }

            if (isset($validated['status']) && $validated['status'] === 'Dibelanjakan') {
                $this->generateAset($pengadaan);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Pengadaan berhasil diperbarui.',
                'data'    => new PengadaanResource($pengadaan->fresh(['pemasok', 'gudang', 'kondisi', 'sumberPerolehan', 'permintaan', 'detailPengadaan.masterBarang', 'aset'])),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui pengadaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $pengadaan = Pengadaan::find($id);
        if (!$pengadaan) return response()->json(['status' => false, 'message' => 'Pengadaan tidak ditemukan.'], 404);

        DB::beginTransaction();
        try {
            $pengadaan->permintaan()->detach();

            // Hapus aset melalui detail_pengadaan (aset -> detail_pengadaan -> pengadaan)
            $detailIds = $pengadaan->detailPengadaan()->pluck('id_detail_pengadaan');
            if ($detailIds->isNotEmpty()) {
                Aset::whereIn('id_detail_pengadaan', $detailIds)->delete();
            }

            $pengadaan->detailPengadaan()->delete();
            $pengadaan->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Pengadaan berhasil dihapus.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus pengadaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateAset(Pengadaan $pengadaan): void
    {
        $asetExists = Aset::where('id_detail_pengadaan', '!=', null)
            ->whereHas('masterBarang', function ($q) use ($pengadaan) {
                $q->whereHas('detailPengadaan', function ($q2) use ($pengadaan) {
                    $q2->where('id_pengadaan', $pengadaan->id_pengadaan);
                });
            })->exists();

        if ($asetExists) {
            return;
        }

        $prefix = \App\Models\Pengaturan::first()->kode_inventaris_prefix ?? 'INV';
        $year = date('Y');
        $idPengadaan = str_pad($pengadaan->id_pengadaan, 3, '0', STR_PAD_LEFT);

        $details = DetailPengadaan::where('id_pengadaan', $pengadaan->id_pengadaan)->get();

        foreach ($details as $detailIndex => $detail) {
            $urutan = str_pad($detailIndex + 1, 3, '0', STR_PAD_LEFT);
            $jumlah = str_pad($detail->jumlah_masuk, 3, '0', STR_PAD_LEFT);

            for ($i = 0; $i < $detail->jumlah_masuk; $i++) {
                $unit = str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                $kodeBarang = 'ASET-' . date('Ymd') . '-' . strtoupper(Str::random(6));

                $maxRetries = 10;
                $attempts = 0;
                while (Aset::where('kode_barang', $kodeBarang)->exists()) {
                    $kodeBarang = 'ASET-' . date('Ymd') . '-' . strtoupper(Str::random(6));
                    $attempts++;
                    if ($attempts >= $maxRetries) {
                        throw new Exception("Gagal membuat kode barang unik setelah {$maxRetries} percobaan.");
                    }
                }

                $kodeInventaris = "{$prefix}-{$year}-{$idPengadaan}-{$urutan}-{$jumlah}-{$unit}";
                $attempts = 0;
                while (Aset::where('kode_inventaris', $kodeInventaris)->exists()) {
                    $attempts++;
                    if ($attempts >= $maxRetries) {
                        throw new Exception("Gagal membuat kode inventaris unik setelah {$maxRetries} percobaan.");
                    }
                    $kodeInventaris = "{$prefix}-{$year}-{$idPengadaan}-{$urutan}-{$jumlah}-{$unit}-{$attempts}";
                }

                Aset::create([
                    'kode_barang'        => $kodeBarang,
                    'kode_inventaris'    => $kodeInventaris,
                    'id_detail_pengadaan'=> $detail->id_detail_pengadaan,
                    'id_master_barang'   => $detail->id_master_barang,
                    'id_kondisi'         => $pengadaan->id_kondisi,
                    'tanggal_registrasi' => now()->format('Y-m-d'),
                    'harga_satuan'       => $detail->harga_satuan ?? 0,
                    'nilai_residu'       => 0,
                    'umur_ekonomi'       => 5,
                    'status'             => 'Di Gudang',
                ]);
            }
        }
    }
}
