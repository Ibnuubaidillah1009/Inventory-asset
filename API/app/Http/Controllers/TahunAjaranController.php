<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TahunAjaranController extends Controller
{
    public function index(): JsonResponse
    {
        $data = TahunAjaran::paginate(20);
        return response()->json([
            'status' => true,
            'message' => 'Daftar tahun ajaran berhasil diambil.',
            'data' => $data
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|integer',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean'
        ]);

        // If newly created is active, deactivate others
        if (isset($validated['is_active']) && $validated['is_active']) {
            TahunAjaran::query()->update(['is_active' => false]);
        }

        $tahunAjaran = TahunAjaran::create($validated);
        return response()->json([
            'status' => true,
            'message' => 'Tahun ajaran berhasil ditambahkan.',
            'data' => $tahunAjaran
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::find($id);
        if (!$tahunAjaran) {
            return response()->json(['status' => false, 'message' => 'Tahun ajaran tidak ditemukan.'], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Detail tahun ajaran berhasil diambil.',
            'data' => $tahunAjaran
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::find($id);
        if (!$tahunAjaran) {
            return response()->json(['status' => false, 'message' => 'Tahun ajaran tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'tahun_ajaran' => 'string|max:20',
            'semester' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date|after:tanggal_mulai',
            'is_active' => 'boolean'
        ]);

        // If updated to active, deactivate others
        if (isset($validated['is_active']) && $validated['is_active']) {
            TahunAjaran::query()->where('id_tahun_ajaran', '!=', $id)->update(['is_active' => false]);
        }

        $tahunAjaran->update($validated);
        return response()->json([
            'status' => true,
            'message' => 'Tahun ajaran berhasil diperbarui.',
            'data' => $tahunAjaran
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::find($id);
        if (!$tahunAjaran) {
            return response()->json(['status' => false, 'message' => 'Tahun ajaran tidak ditemukan.'], 404);
        }
        $tahunAjaran->delete();
        return response()->json([
            'status' => true,
            'message' => 'Tahun ajaran berhasil dihapus.'
        ]);
    }

    public function setActive(string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::find($id);
        if (!$tahunAjaran) {
            return response()->json(['status' => false, 'message' => 'Tahun ajaran tidak ditemukan.'], 404);
        }

        // Deactivate all
        TahunAjaran::query()->update(['is_active' => false]);
        
        // Set specific to active
        $tahunAjaran->update(['is_active' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Tahun ajaran berhasil diaktifkan.',
            'data' => $tahunAjaran
        ]);
    }
}
