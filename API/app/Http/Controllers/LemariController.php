<?php

namespace App\Http\Controllers;

use App\Models\Lemari;
use App\Http\Resources\LemariResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LemariController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Lemari::with('ruang')->paginate(20);
        return LemariResource::collection($data)->additional([
            'status'  => true,
            'message' => 'Daftar lemari berhasil diambil.',
        ])->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_lemari' => 'required|string|max:50|unique:lemari,kode_lemari',
            'nama' => 'required|string|max:100',
            'id_ruang' => 'required|integer|exists:ruang,id_ruang',
            'nomor_rak' => 'nullable|string|max:50',
        ]);

        $lemari = Lemari::create($validated);
        $lemari->load('ruang');
        return response()->json([
            'status' => true,
            'message' => 'Lemari berhasil ditambahkan.',
            'data' => $lemari
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $lemari = Lemari::with('ruang')->find($id);
        if (!$lemari) {
            return response()->json(['status' => false, 'message' => 'Lemari tidak ditemukan.'], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Detail lemari berhasil diambil.',
            'data' => $lemari
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $lemari = Lemari::find($id);
        if (!$lemari) {
            return response()->json(['status' => false, 'message' => 'Lemari tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'kode_lemari' => 'string|max:50|unique:lemari,kode_lemari,' . $id . ',id_lemari',
            'nama' => 'string|max:100',
            'id_ruang' => 'integer|exists:ruang,id_ruang',
            'nomor_rak' => 'nullable|string|max:50',
        ]);

        $lemari->update($validated);
        $lemari->load('ruang');
        return response()->json([
            'status' => true,
            'message' => 'Lemari berhasil diperbarui.',
            'data' => $lemari
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $lemari = Lemari::find($id);
        if (!$lemari) {
            return response()->json(['status' => false, 'message' => 'Lemari tidak ditemukan.'], 404);
        }
        $lemari->delete();
        return response()->json([
            'status' => true,
            'message' => 'Lemari berhasil dihapus.'
        ]);
    }
}
