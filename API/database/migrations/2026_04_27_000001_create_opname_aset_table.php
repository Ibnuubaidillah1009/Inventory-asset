<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname_aset', function (Blueprint $table) {
            $table->id('id_opname_aset');
            $table->string('kode_barang', 50);
            $table->date('tanggal_opname');
            $table->string('kondisi_ditemukan', 50)->comment('Kondisi barang saat opname: Baik, Rusak Ringan, Rusak Berat, Hilang');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('kode_barang')->references('kode_barang')->on('aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname_aset');
    }
};
