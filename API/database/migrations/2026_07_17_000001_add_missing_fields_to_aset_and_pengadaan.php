<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================
        // TAMBAH KOLOM KE TABEL aset
        // =====================================================
        Schema::table('aset', function (Blueprint $table) {
            $table->unsignedInteger('id_kondisi')->nullable()->after('id_master_barang');
            $table->string('model_tipe', 150)->nullable()->after('no_seri');
            $table->string('barcode', 100)->nullable()->after('model_tipe');
            $table->string('penanggung_jawab', 150)->nullable()->after('id_jurusan');
            $table->date('tanggal_garansi_mulai')->nullable()->after('keterangan');
            $table->date('tanggal_garansi_akhir')->nullable()->after('tanggal_garansi_mulai');
            $table->text('info_garansi')->nullable()->after('tanggal_garansi_akhir');
            $table->enum('metode_penyusutan', ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi', 'Lainnya'])->nullable()->after('info_garansi');
            $table->date('jadwal_pemeliharaan_berikutnya')->nullable()->after('metode_penyusutan');
            $table->string('nomor_polis_asuransi', 100)->nullable()->after('jadwal_pemeliharaan_berikutnya');
            $table->decimal('nilai_pertanggungan', 15, 2)->nullable()->after('nomor_polis_asuransi');
            $table->date('tanggal_akhir_asuransi')->nullable()->after('nilai_pertanggungan');
        });

        // =====================================================
        // TAMBAH KOLOM KE TABEL pengadaan
        // =====================================================
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->string('nomor_po', 100)->nullable()->after('id_pengadaan');
            $table->string('nomor_faktur', 100)->nullable()->after('nomor_po');
            $table->decimal('persentase_ppn', 5, 2)->nullable()->after('total_harga');
            $table->decimal('nominal_ppn', 15, 2)->nullable()->after('persentase_ppn');
            $table->decimal('grand_total', 15, 2)->nullable()->after('nominal_ppn');
            $table->date('tanggal_pengiriman')->nullable()->after('sumber_perolehan');
            $table->string('nomor_po_lampiran', 255)->nullable()->after('tanggal_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropColumn([
                'id_kondisi',
                'model_tipe',
                'barcode',
                'penanggung_jawab',
                'tanggal_garansi_mulai',
                'tanggal_garansi_akhir',
                'info_garansi',
                'metode_penyusutan',
                'jadwal_pemeliharaan_berikutnya',
                'nomor_polis_asuransi',
                'nilai_pertanggungan',
                'tanggal_akhir_asuransi',
            ]);
        });

        Schema::table('pengadaan', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_po',
                'nomor_faktur',
                'persentase_ppn',
                'nominal_ppn',
                'grand_total',
                'tanggal_pengiriman',
                'nomor_po_lampiran',
            ]);
        });
    }
};
