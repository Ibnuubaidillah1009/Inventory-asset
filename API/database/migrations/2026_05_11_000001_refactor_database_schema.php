<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. TABEL PENGADAAN — ALTER
        // =====================================================================
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->renameColumn('nomor_pengadaan', 'kode_inventaris');
        });
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->renameColumn('id_master_barang', 'kode_barang');
            $table->renameColumn('nomor_permintaan', 'kode_permintaan');
        });
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->string('kode_barang', 50)->change();
            $table->string('no_seri', 100)->nullable()->after('kode_gudang');
            $table->text('sumber_perolehan')->nullable()->after('no_seri');
            $table->decimal('harga_satuan', 15, 2)->default(0)->after('sumber_perolehan');
            $table->decimal('nilai_residu', 15, 2)->default(0)->after('harga_satuan');
            $table->integer('umur_ekonomi')->nullable()->after('nilai_residu');
        });

        // detail_pengadaan: nomor_pengadaan → kode_inventaris
        Schema::table('detail_pengadaan', function (Blueprint $table) {
            $table->renameColumn('nomor_pengadaan', 'kode_inventaris');
        });

        // =====================================================================
        // 2. TABEL ASET_TANAH — ALTER
        // =====================================================================
        Schema::table('aset_tanah', function (Blueprint $table) {
            $table->renameColumn('id_tanah', 'kode_tanah');
            $table->renameColumn('nama_tanah', 'nama_pemilik');
            $table->renameColumn('alamat_lokasi', 'letak_tanah');
            $table->renameColumn('keterangan', 'penggunaan');
        });
        Schema::table('aset_tanah', function (Blueprint $table) {
            $table->renameColumn('tahun_pengadaan', 'tanggal_perolehan');
        });
        Schema::table('aset_tanah', function (Blueprint $table) {
            $table->date('tanggal_perolehan')->nullable()->change();
            $table->enum('sumber_perolehan', ['beli', 'sumbangan', 'hibah', 'tukar guling', 'lainnya'])->nullable()->after('penggunaan');
        });

        // =====================================================================
        // 3. TABEL ASET_BANGUNAN — ALTER
        // =====================================================================
        Schema::table('aset_bangunan', function (Blueprint $table) {
            $table->renameColumn('id_bangunan', 'kode_bangunan');
        });
        Schema::table('aset_bangunan', function (Blueprint $table) {
            $table->renameColumn('tahun_bangun', 'tanggal_bangunan');
        });
        Schema::table('aset_bangunan', function (Blueprint $table) {
            $table->date('tanggal_bangunan')->nullable()->change();
            if (Schema::hasColumn('aset_bangunan', 'id_tanah')) {
                $table->dropColumn('id_tanah');
            }
            if (Schema::hasColumn('aset_bangunan', 'konstruksi_bertingkat')) {
                $table->dropColumn('konstruksi_bertingkat');
            }
            if (Schema::hasColumn('aset_bangunan', 'konstruksi_beton')) {
                $table->dropColumn('konstruksi_beton');
            }
        });
        Schema::table('aset_bangunan', function (Blueprint $table) {
            $table->unsignedInteger('id_kondisi')->nullable()->after('alamat_bangunan');
            $table->decimal('ukuran_p', 10, 2)->nullable()->after('id_kondisi');
            $table->decimal('ukuran_l', 10, 2)->nullable()->after('ukuran_p');
            $table->string('konstruksi', 255)->nullable()->after('ukuran_l');
        });

        // =====================================================================
        // 4. TABEL MUTASI — ALTER
        // =====================================================================
        Schema::table('mutasi', function (Blueprint $table) {
            $table->renameColumn('kode_barang', 'kode_inventaris');
            $table->renameColumn('id_ruang_asal', 'id_lokasi_asal');
            $table->renameColumn('id_ruang_tujuan', 'id_lokasi_tujuan');
        });
        Schema::table('mutasi', function (Blueprint $table) {
            if (Schema::hasColumn('mutasi', 'id_penanggung_jawab')) {
                $table->dropColumn('id_penanggung_jawab');
            }
            $table->integer('jumlah_mutasi')->default(1)->after('alasan_mutasi');
        });

        // =====================================================================
        // 5. TABEL OPNAME_ASET — ALTER
        // =====================================================================
        Schema::table('opname_aset', function (Blueprint $table) {
            if (Schema::hasColumn('opname_aset', 'kode_barang')) {
                $table->dropColumn('kode_barang');
            }
            if (Schema::hasColumn('opname_aset', 'kondisi_ditemukan')) {
                $table->dropColumn('kondisi_ditemukan');
            }
            if (Schema::hasColumn('opname_aset', 'id_pemeriksa')) {
                $table->dropColumn('id_pemeriksa');
            }
        });
        Schema::table('opname_aset', function (Blueprint $table) {
            $table->string('kode_inventaris', 50)->after('id_opname_aset');
            $table->unsignedInteger('id_kondisi')->nullable()->after('tanggal_opname');
        });

        // =====================================================================
        // 6. TABEL PERMINTAAN — ALTER
        // =====================================================================
        Schema::table('permintaan', function (Blueprint $table) {
            $table->renameColumn('nomor_permintaan', 'kode_permintaan');
        });
        Schema::table('permintaan', function (Blueprint $table) {
            $table->renameColumn('id_pemohon', 'nama_pemohon');
        });
        Schema::table('permintaan', function (Blueprint $table) {
            $table->string('nama_pemohon', 150)->change();
            $table->text('alasan_disetujui')->nullable()->after('keterangan');
        });

        // detail_permintaan: nomor_permintaan → kode_permintaan
        if (Schema::hasColumn('detail_permintaan', 'nomor_permintaan')) {
            Schema::table('detail_permintaan', function (Blueprint $table) {
                $table->renameColumn('nomor_permintaan', 'kode_permintaan');
            });
        }
        if (Schema::hasColumn('detail_permintaan', 'id_permintaan')) {
            Schema::table('detail_permintaan', function (Blueprint $table) {
                $table->renameColumn('id_permintaan', 'kode_permintaan');
            });
            Schema::table('detail_permintaan', function (Blueprint $table) {
                $table->string('kode_permintaan', 50)->change();
            });
        }

        // =====================================================================
        // 7. TABEL ASET — ALTER (remove columns)
        // =====================================================================
        Schema::table('aset', function (Blueprint $table) {
            $columns = ['id_ruang', 'nilai_residu', 'id_status', 'id_kondisi'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('aset', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // =====================================================================
        // 8. TABEL PEMINJAMAN — ALTER
        // =====================================================================
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->renameColumn('id_peminjam', 'nama_peminjam');
        });
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('nama_peminjam', 150)->change();
        });

        // =====================================================================
        // 9. TABEL BARU: barang_non_aktif
        // =====================================================================
        Schema::create('barang_non_aktif', function (Blueprint $table) {
            $table->increments('id_nonaktif');
            $table->string('kode_inventaris', 50);
            $table->unsignedInteger('id_status');
            $table->integer('jumlah_nonaktif')->default(1);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
        });

        // =====================================================================
        // 10. TABEL BARU: tanah_non_aktif
        // =====================================================================
        Schema::create('tanah_non_aktif', function (Blueprint $table) {
            $table->increments('id_nonaktif');
            $table->integer('kode_tanah');
            $table->unsignedInteger('id_status');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
        });

        // =====================================================================
        // 11. TABEL BARU: bangunan_non_aktif
        // =====================================================================
        Schema::create('bangunan_non_aktif', function (Blueprint $table) {
            $table->increments('id_nonaktif');
            $table->integer('kode_bangunan');
            $table->unsignedInteger('id_status');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
        });

        // =====================================================================
        // 12. TABEL BARU: aset_habis_pakai
        // =====================================================================
        Schema::create('aset_habis_pakai', function (Blueprint $table) {
            $table->string('kode_barang', 50)->primary();
            $table->string('nama_barang', 150);
            $table->unsignedInteger('id_satuan')->nullable();
            $table->integer('stok_minimal')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('gambar', 255)->nullable();
        });

        // =====================================================================
        // 13. TABEL BARU: pengadaan_habis_pakai
        // =====================================================================
        Schema::create('pengadaan_habis_pakai', function (Blueprint $table) {
            $table->string('kode_inventaris', 50)->primary();
            $table->date('tanggal_pengadaan');
            $table->string('kode_barang', 50);
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0.00);
            $table->unsignedInteger('id_pemasok')->nullable();
            $table->string('kode_gudang', 20)->nullable();
            $table->text('keterangan')->nullable();
        });

        // =====================================================================
        // 14. TABEL BARANG_KELUAR — ALTER
        // =====================================================================
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->renameColumn('id_barang_keluar', 'no_transaksi');
        });
        Schema::table('barang_keluar', function (Blueprint $table) {
            if (Schema::hasColumn('barang_keluar', 'id_master_barang')) {
                $table->dropColumn('id_master_barang');
            }
            if (Schema::hasColumn('barang_keluar', 'kode_gudang')) {
                $table->dropColumn('kode_gudang');
            }
        });
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->string('kode_inventaris', 50)->after('no_transaksi');
        });
    }

    public function down(): void
    {
        // Reverse barang_keluar
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn('kode_inventaris');
        });
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->renameColumn('no_transaksi', 'id_barang_keluar');
        });

        // Drop new tables
        Schema::dropIfExists('pengadaan_habis_pakai');
        Schema::dropIfExists('aset_habis_pakai');
        Schema::dropIfExists('bangunan_non_aktif');
        Schema::dropIfExists('tanah_non_aktif');
        Schema::dropIfExists('barang_non_aktif');

        // Reverse peminjaman
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->renameColumn('nama_peminjam', 'id_peminjam');
        });

        // Reverse aset
        Schema::table('aset', function (Blueprint $table) {
            $table->unsignedInteger('id_ruang')->nullable();
            $table->decimal('nilai_residu', 15, 2)->nullable();
            $table->unsignedInteger('id_status')->nullable();
            $table->unsignedInteger('id_kondisi')->nullable();
        });

        // Reverse permintaan
        Schema::table('permintaan', function (Blueprint $table) {
            $table->dropColumn('alasan_disetujui');
            $table->renameColumn('nama_pemohon', 'id_pemohon');
            $table->renameColumn('kode_permintaan', 'nomor_permintaan');
        });

        // Reverse opname_aset
        Schema::table('opname_aset', function (Blueprint $table) {
            $table->dropColumn(['kode_inventaris', 'id_kondisi']);
            $table->string('kode_barang', 50)->nullable();
            $table->string('kondisi_ditemukan')->nullable();
            $table->unsignedInteger('id_pemeriksa')->nullable();
        });

        // Reverse mutasi
        Schema::table('mutasi', function (Blueprint $table) {
            $table->dropColumn('jumlah_mutasi');
            $table->unsignedInteger('id_penanggung_jawab')->nullable();
            $table->renameColumn('kode_inventaris', 'kode_barang');
            $table->renameColumn('id_lokasi_asal', 'id_ruang_asal');
            $table->renameColumn('id_lokasi_tujuan', 'id_ruang_tujuan');
        });

        // Reverse aset_bangunan
        Schema::table('aset_bangunan', function (Blueprint $table) {
            $table->dropColumn(['id_kondisi', 'ukuran_p', 'ukuran_l', 'konstruksi']);
            $table->renameColumn('tanggal_bangunan', 'tahun_bangun');
            $table->renameColumn('kode_bangunan', 'id_bangunan');
        });

        // Reverse aset_tanah
        Schema::table('aset_tanah', function (Blueprint $table) {
            $table->dropColumn('sumber_perolehan');
            $table->renameColumn('tanggal_perolehan', 'tahun_pengadaan');
            $table->renameColumn('letak_tanah', 'alamat_lokasi');
            $table->renameColumn('penggunaan', 'keterangan');
            $table->renameColumn('nama_pemilik', 'nama_tanah');
            $table->renameColumn('kode_tanah', 'id_tanah');
        });

        // Reverse pengadaan
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->dropColumn(['no_seri', 'sumber_perolehan', 'harga_satuan', 'nilai_residu', 'umur_ekonomi']);
            $table->renameColumn('kode_permintaan', 'nomor_permintaan');
            $table->renameColumn('kode_barang', 'id_master_barang');
            $table->renameColumn('kode_inventaris', 'nomor_pengadaan');
        });

        // Reverse detail_pengadaan
        Schema::table('detail_pengadaan', function (Blueprint $table) {
            $table->renameColumn('kode_inventaris', 'nomor_pengadaan');
        });
    }
};
