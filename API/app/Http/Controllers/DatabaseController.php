<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

/**
 * @OA\Schema(schema="DatabaseResetResponse", type="object",
 *     @OA\Property(property="message", type="string", example="Database berhasil direset.")
 * )
 * @OA\Schema(schema="DatabaseRestoreResponse", type="object",
 *     @OA\Property(property="message", type="string", example="Database berhasil direstore.")
 * )
 * @OA\Schema(schema="DatabaseChangeConnectionRequest", type="object", required={"db_host","db_name","db_user"},
 *     @OA\Property(property="db_host", type="string", example="127.0.0.1"),
 *     @OA\Property(property="db_name", type="string", example="inventaris_sekolah_db"),
 *     @OA\Property(property="db_user", type="string", example="root"),
 *     @OA\Property(property="db_pass", type="string", nullable=true, example="secret123"),
 *     @OA\Property(property="db_port", type="string", example="3307")  
 * )
 * @OA\Schema(schema="DatabaseChangeConnectionResponse", type="object",
 *     @OA\Property(property="message", type="string", example="Koneksi berhasil diubah.")
 * )
 * @OA\Schema(schema="DatabaseErrorResponse", type="object",
 *     @OA\Property(property="error", type="string", example="Error message")
 * )
 */
class DatabaseController extends Controller
{
    /**
     * @OA\Post(path="/database/reset", operationId="resetDatabase", tags={"Database"}, summary="Reset database", description="Menghapus semua tabel dan menjalankan ulang migrasi serta seeder. PERHATIAN: Semua data akan hilang!", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Database berhasil direset", @OA\JsonContent(ref="#/components/schemas/DatabaseResetResponse")),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Gagal mereset database", @OA\JsonContent(ref="#/components/schemas/DatabaseErrorResponse"))
     * )
     */
    public function reset()
    {
        try {
            // Hapus semua tabel dan jalankan ulang migrasi serta seeder
            Artisan::call('migrate:fresh', ['--seed' => true]);
            return response()->json(['message' => 'Database berhasil direset.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(path="/database/backup", operationId="backupDatabase", tags={"Database"}, summary="Backup database", description="Membuat file backup SQL dari database saat ini dan mengembalikannya sebagai file download.", security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="File backup SQL berhasil diunduh",
     *         @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Gagal membuat backup database")
     * )
     */
    public function backup()
    {
        $filename = "backup-" . date('Y-m-d-H-i-s') . ".sql";
        $path = storage_path('app/backups/' . $filename);

        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');

        // Menjalankan mysqldump via command line
        $command = "mysqldump --user={$user} --password={$password} {$database} > {$path}";
        exec($command);

        return response()->download($path);
    }

    /**
     * @OA\Post(path="/database/restore", operationId="restoreDatabase", tags={"Database"}, summary="Restore database", description="Mengembalikan database dari file SQL yang diunggah. PERHATIAN: Data saat ini akan ditimpa!", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\MediaType(mediaType="multipart/form-data",
     *             @OA\Schema(required={"sql_file"}, @OA\Property(property="sql_file", type="string", format="binary", description="File SQL untuk restore"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Database berhasil direstore", @OA\JsonContent(ref="#/components/schemas/DatabaseRestoreResponse")),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validasi gagal"),
     *     @OA\Response(response=500, description="Gagal merestore database", @OA\JsonContent(ref="#/components/schemas/DatabaseErrorResponse"))
     * )
     */
    public function restore(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt'
        ]);

        try {
            $sql = file_get_contents($request->file('sql_file')->getRealPath());

            // Disable foreign key checks sementara agar tidak error saat drop table
            DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sql);
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json(['message' => 'Database berhasil direstore.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal merestore database: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(path="/database/change-connection", operationId="changeConnectionDatabase", tags={"Database"}, summary="Ubah koneksi database", description="Mengubah konfigurasi koneksi database di file .env.", security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/DatabaseChangeConnectionRequest")),
     *     @OA\Response(response=200, description="Koneksi berhasil diubah", @OA\JsonContent(ref="#/components/schemas/DatabaseChangeConnectionResponse")),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function changeConnection(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_pass' => 'nullable',
            'port' => 'required',
        ]);

        // Logika untuk mengubah file .env
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'DB_DATABASE=' . env('DB_DATABASE'),
                'DB_DATABASE=' . $request->db_name,
                file_get_contents($path)
            ));
            file_put_contents($path, str_replace(
                'DB_HOST=' . env('DB_HOST'),
                'DB_HOST=' . $request->db_host,
                file_get_contents($path)
            ));
            file_put_contents($path, str_replace(
                'DB_USERNAME=' . env('DB_USERNAME'),
                'DB_USERNAME=' . $request->db_user,
                file_get_contents($path)
            ));
            file_put_contents($path, str_replace(
                'DB_PASSWORD=' . env('DB_PASSWORD'),
                'DB_PASSWORD=' . $request->db_pass,
                file_get_contents($path)
            ));
            file_put_contents($path, str_replace(
                'DB_PORT=' . env('DB_PORT'),
                'DB_PORT=' . $request->db_port,
                file_get_contents($path)
            ));
        }

        // Clear cache agar konfigurasi baru terbaca
        Artisan::call('config:clear');

        return response()->json(['message' => 'Koneksi berhasil diubah.']);
    }
}
