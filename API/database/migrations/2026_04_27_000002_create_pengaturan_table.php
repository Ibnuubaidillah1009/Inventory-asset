<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id('id_pengaturan');
            $table->string('nama_instansi', 255)->nullable();
            $table->text('alamat_instansi')->nullable();
            $table->string('wallpaper_aplikasi', 255)->nullable()->comment('Path logo lembaga');
            $table->string('telpon', 30)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('kepala_sekolah', 255)->nullable();
            $table->string('NIP', 50)->nullable();
            $table->string('bagian_inventaris', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
