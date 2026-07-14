<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * ============================================================
 *  SCHEMA DEFINITIONS – Auth Module
 * ============================================================
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"username","password"},
 *     description="Payload untuk login pengguna",
 *     @OA\Property(property="username", type="string", example="admin"),
 *     @OA\Property(property="password", type="string", example="password123")
 * )
 *
 * @OA\Schema(
 *     schema="LoginData",
 *     type="object",
 *     description="Data hasil login berisi pengguna dan token",
 *     @OA\Property(property="pengguna", ref="#/components/schemas/PenggunaResource"),
 *     @OA\Property(property="token", type="string", example="1|abc123tokenxyz")
 * )
 *
 * @OA\Schema(
 *     schema="LogoutResponse",
 *     type="object",
 *     description="Response wrapper untuk logout berhasil",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Logout berhasil.")
 * )
 *
 * @OA\Schema(
 *     schema="LoginResponse",
 *     type="object",
 *     description="Response wrapper untuk login berhasil",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Login berhasil."),
 *     @OA\Property(property="data", ref="#/components/schemas/LoginData")
 * )
 *
 * @OA\Schema(
 *     schema="MeResponse",
 *     type="object",
 *     description="Response wrapper untuk data pengguna yang sedang login",
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Data pengguna berhasil diambil."),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/PenggunaResource",
 *         nullable=true
 *     )
 * )
 */
class AuthController extends Controller
{
    /**
     * Login — menggunakan username & password, mengembalikan Sanctum token.
     *
     * @OA\Post(
     *     path="/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Login pengguna",
     *     description="Autentikasi pengguna menggunakan username dan password, mengembalikan token Sanctum.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(ref="#/components/schemas/LoginResponse")
     *     ),
     *     @OA\Response(response=401, description="Username atau password salah"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $pengguna = Pengguna::where('username', $request->username)->first();

        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Buat token Sanctum
        $token = $pengguna->createToken('auth_token')->plainTextToken;

        $penggunaLengkap = $pengguna->load(['peran', 'kelas', 'mapel', 'unit']);
        $menuTree = [];
        if ($penggunaLengkap->peran) {
            $menuTree = \App\Models\Akses::getMenuTreeForPeran($penggunaLengkap->id_peran);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'pengguna'     => $penggunaLengkap,
                'token' => $token,
            ],
            'menu_tree' => $menuTree
        ]);
    }

    /**
     * Logout — revoke token yang sedang digunakan.
     *
     * @OA\Post(
     *     path="/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Logout pengguna",
     *     description="Revoke token Sanctum yang sedang digunakan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout berhasil",
     *         @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid atau tidak diberikan")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Me — mengembalikan data pengguna yang sedang login beserta relasinya.
     *
     * @OA\Get(
     *     path="/me",
     *     operationId="me",
     *     tags={"Auth"},
     *     summary="Profil pengguna yang sedang login",
     *     description="Mengembalikan data pengguna yang sedang login beserta relasi peran, akses, kelas, mapel, dan unit.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data pengguna berhasil diambil",
     *         @OA\JsonContent(ref="#/components/schemas/MeResponse")
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid atau tidak diberikan")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        $pengguna = $request->user()->load(['peran.aksesList', 'kelas.rombel.jurusan', 'mapel', 'unit']);

        $menuTree = [];
        if ($pengguna->peran) {
            $menuTree = \App\Models\Akses::getMenuTreeForPeran($pengguna->id_peran);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Data pengguna berhasil diambil.',
            'data'    => $pengguna,
            'menu_tree' => $menuTree
        ]);
    }
}
