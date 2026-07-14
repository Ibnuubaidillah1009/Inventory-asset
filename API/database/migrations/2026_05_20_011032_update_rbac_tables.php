<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrasi untuk restrukturisasi tabel RBAC:
     * 1. Tabel `akses`: tambah id_parent + timestamps, hapus kolom hak_*
     * 2. Tabel `peran_akses`: pastikan kolom hak_* ada di pivot
     */
    public function up(): void
    {
        // =====================================================================
        // 1. RESTRUKTURISASI TABEL `akses`
        // =====================================================================
        Schema::table('akses', function (Blueprint $table) {
            // Tambah id_parent jika belum ada
            if (!Schema::hasColumn('akses', 'id_parent')) {
                $table->unsignedInteger('id_parent')->nullable()->after('id_akses');
            }

            // Tambah timestamps jika belum ada
            if (!Schema::hasColumn('akses', 'created_at')) {
                $table->timestamps();
            }

            // Hapus kolom hak_* dari tabel akses (dipindah ke peran_akses)
            $columnsToRemove = ['hak_buat', 'hak_baca', 'hak_ubah', 'hak_hapus'];
            foreach ($columnsToRemove as $col) {
                if (Schema::hasColumn('akses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // =====================================================================
        // 2. PASTIKAN TABEL `peran_akses` MEMILIKI KOLOM HAK
        // =====================================================================
        Schema::table('peran_akses', function (Blueprint $table) {
            if (!Schema::hasColumn('peran_akses', 'hak_baca')) {
                $table->tinyInteger('hak_baca')->default(0)->after('id_akses');
            }
            if (!Schema::hasColumn('peran_akses', 'hak_buat')) {
                $table->tinyInteger('hak_buat')->default(0)->after('hak_baca');
            }
            if (!Schema::hasColumn('peran_akses', 'hak_ubah')) {
                $table->tinyInteger('hak_ubah')->default(0)->after('hak_buat');
            }
            if (!Schema::hasColumn('peran_akses', 'hak_hapus')) {
                $table->tinyInteger('hak_hapus')->default(0)->after('hak_ubah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akses', function (Blueprint $table) {
            if (Schema::hasColumn('akses', 'id_parent')) {
                $table->dropColumn('id_parent');
            }
            if (Schema::hasColumn('akses', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
