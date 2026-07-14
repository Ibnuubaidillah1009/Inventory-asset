<?php

namespace App\Http\Controllers;

use App\Models\SumberPerolehan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SumberPerolehanController extends Controller
{
    public function index(): JsonResponse
    {
        $data = SumberPerolehan::all();
        return response()->json([
            'status' => true,
            'message' => 'Daftar sumber perolehan berhasil diambil.',
            'data' => $data
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_sumber' => 'required|string|max:50|unique:sumber_perolehan,kode_sumber',
            'nama' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $sumberPerolehan = SumberPerolehan::create($validated);
        return response()->json([
            'status' => true,
            'message' => 'Sumber perolehan berhasil ditambahkan.',
            'data' => $sumberPerolehan
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $sumberPerolehan = SumberPerolehan::find($id);
        if (!$sumberPerolehan) {
            return response()->json(['status' => false, 'message' => 'Sumber perolehan tidak ditemukan.'], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Detail sumber perolehan berhasil diambil.',
            'data' => $sumberPerolehan
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $sumberPerolehan = SumberPerolehan::find($id);
        if (!$sumberPerolehan) {
            return response()->json(['status' => false, 'message' => 'Sumber perolehan tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'kode_sumber' => 'string|max:50|unique:sumber_perolehan,kode_sumber,' . $id . ',id_sumber_perolehan',
            'nama' => 'string|max:100',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $sumberPerolehan->update($validated);
        return response()->json([
            'status' => true,
            'message' => 'Sumber perolehan berhasil diperbarui.',
            'data' => $sumberPerolehan
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $sumberPerolehan = SumberPerolehan::find($id);
        if (!$sumberPerolehan) {
            return response()->json(['status' => false, 'message' => 'Sumber perolehan tidak ditemukan.'], 404);
        }
        $sumberPerolehan->delete();
        return response()->json([
            'status' => true,
            'message' => 'Sumber perolehan berhasil dihapus.'
        ]);
    }
}
