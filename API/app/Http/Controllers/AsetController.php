<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Http\Requests\StoreAsetRequest;
use App\Http\Requests\UpdateAsetRequest;
use App\Http\Resources\AsetResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsetController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Aset::with(['masterBarang.kategori', 'masterBarang.merek', 'masterBarang.satuan', 'kondisi', 'jurusan', 'ruang', 'lokasi'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_barang', 'like', "%{$search}%")
                      ->orWhere('kode_inventaris', 'like', "%{$search}%")
                      ->orWhere('no_seri', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%")
                      ->orWhere('penanggung_jawab', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return AsetResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar aset berhasil diambil.',
        ])->response();
    }

    public function store(StoreAsetRequest $request): JsonResponse
    {
        $aset = Aset::create($request->validated());
        $aset->load(['masterBarang', 'kondisi', 'jurusan', 'ruang', 'lokasi']);

        return response()->json([
            'status'  => true,
            'message' => 'Aset berhasil ditambahkan.',
            'data'    => new AsetResource($aset),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $aset = Aset::with([
            'masterBarang.kategori', 'masterBarang.merek', 'masterBarang.satuan',
            'kondisi', 'jurusan', 'ruang', 'lokasi'
        ])->find($id);

        if (!$aset) {
            return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail aset berhasil diambil.',
            'data'    => new AsetResource($aset),
        ]);
    }

    public function update(UpdateAsetRequest $request, string $id): JsonResponse
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        $aset->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Aset berhasil diperbarui.',
            'data'    => new AsetResource($aset->fresh(['masterBarang', 'kondisi', 'jurusan', 'ruang', 'lokasi'])),
        ]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        $request->validate([
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $aset->update(['status' => $request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Status aset berhasil diperbarui.',
            'data'    => new AsetResource($aset->fresh(['masterBarang', 'kondisi', 'jurusan', 'ruang', 'lokasi'])),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        $aset->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Aset berhasil dihapus.',
        ]);
    }
}
