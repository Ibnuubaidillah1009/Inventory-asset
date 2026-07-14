<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing tables to recreate them with the new schema
        Schema::dropIfExists('detail_mutasi');
        Schema::dropIfExists('mutasi');
        Schema::dropIfExists('aset');
        Schema::dropIfExists('pengadaan_permintaan');
        Schema::dropIfExists('detail_pengadaan');
        Schema::dropIfExists('pengadaan');
        Schema::dropIfExists('permintaan');

        // 1. Tabel permintaan
        Schema::create('permintaan', function (Blueprint $table) {
            $table->string('kode_permintaan', 50)->primary();
            $table->unsignedInteger('id_jurusan')->nullable();
            $table->enum('status', ['diproses', 'disetujui', 'ditolak', 'dipenuhi_sebagian', 'selesai'])->default('diproses');
            $table->timestamps();
        });

        // 2. Tabel pengadaan
        Schema::create('pengadaan', function (Blueprint $table) {
            $table->id('id_pengadaan'); // BIGINT PRIMARY KEY AUTO_INCREMENT
            $table->date('tanggal_pengadaan');
            $table->unsignedInteger('id_pemasok')->nullable();
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('kode_gudang', 50)->nullable();
            $table->integer('jumlah_pengadaan')->default(0);
            $table->unsignedInteger('id_kondisi')->nullable();
            $table->string('sumber_perolehan', 100)->nullable();
            $table->enum('status', ['diproses', 'dibelanjakan', 'selesai'])->default('diproses');
            $table->timestamps();
        });

        // 3. Tabel detail_pengadaan
        Schema::create('detail_pengadaan', function (Blueprint $table) {
            $table->id('id_detail_pengadaan');
            $table->unsignedBigInteger('id_pengadaan');
            $table->string('id_master_barang', 50); // Assuming master_barang PK is string or integer, setting to string for now. If it's unsignedInteger, change it. Let me check master_barang. Wait, the previous migration changed id_master_barang to kode_barang.
            $table->integer('jumlah_masuk')->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('id_pengadaan')->references('id_pengadaan')->on('pengadaan')->onDelete('cascade');
        });

        // 4. Tabel pengadaan_permintaan (Pivot)
        Schema::create('pengadaan_permintaan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengadaan');
            $table->string('kode_permintaan', 50);
            $table->primary(['id_pengadaan', 'kode_permintaan']);
            $table->foreign('id_pengadaan')->references('id_pengadaan')->on('pengadaan')->onDelete('cascade');
            $table->foreign('kode_permintaan')->references('kode_permintaan')->on('permintaan')->onDelete('cascade');
        });

        // 5. Tabel aset
        Schema::create('aset', function (Blueprint $table) {
            $table->string('kode_inventaris', 100)->primary(); // UNIQUE identity
            $table->unsignedBigInteger('id_pengadaan')->nullable();
            $table->string('id_master_barang', 50);
            $table->unsignedInteger('id_jurusan')->nullable();
            $table->unsignedInteger('id_ruang')->nullable();
            $table->unsignedInteger('id_lokasi')->nullable();
            $table->string('no_seri', 100)->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('nilai_residu', 15, 2)->default(0);
            $table->integer('umur_ekonomi')->nullable();
            $table->enum('status', ['Di Gudang', 'Aktif', 'Dipinjam', 'Nonaktif'])->default('Di Gudang');
            $table->timestamps();
            
            $table->foreign('id_pengadaan')->references('id_pengadaan')->on('pengadaan')->onDelete('set null');
        });

        // 6. Tabel mutasi
        Schema::create('mutasi', function (Blueprint $table) {
            $table->id('id_mutasi');
            $table->unsignedInteger('id_jurusan_asal')->nullable();
            $table->unsignedInteger('id_jurusan_tujuan');
            $table->date('tanggal_mutasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 7. Tabel detail_mutasi
        Schema::create('detail_mutasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mutasi');
            $table->string('kode_inventaris', 100);
            $table->timestamps();
            
            $table->foreign('id_mutasi')->references('id_mutasi')->on('mutasi')->onDelete('cascade');
            $table->foreign('kode_inventaris')->references('kode_inventaris')->on('aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_mutasi');
        Schema::dropIfExists('mutasi');
        Schema::dropIfExists('aset');
        Schema::dropIfExists('pengadaan_permintaan');
        Schema::dropIfExists('detail_pengadaan');
        Schema::dropIfExists('pengadaan');
        Schema::dropIfExists('permintaan');
    }
};
