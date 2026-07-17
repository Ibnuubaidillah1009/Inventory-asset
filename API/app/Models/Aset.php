<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $table = 'aset';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'kode_barang',
        'id_detail_pengadaan',
        'id_master_barang',
        'id_jurusan',
        'penanggung_jawab',
        'id_ruang',
        'id_lokasi',
        'no_seri',
        'model_tipe',
        'barcode',
        'harga_satuan',
        'nilai_residu',
        'umur_ekonomi',
        'kode_inventaris',
        'status',
        'id_kondisi',
        'tanggal_registrasi',
        'gambar',
        'keterangan',
        'metode_penyusutan',
        'tanggal_garansi_mulai',
        'tanggal_garansi_akhir',
        'info_garansi',
        'jadwal_pemeliharaan_berikutnya',
        'nomor_polis_asuransi',
        'nilai_pertanggungan',
        'tanggal_akhir_asuransi',
    ];

    protected $casts = [
        'harga_satuan'                   => 'decimal:2',
        'nilai_residu'                   => 'decimal:2',
        'nilai_pertanggungan'            => 'decimal:2',
        'tanggal_garansi_mulai'          => 'date',
        'tanggal_garansi_akhir'          => 'date',
        'jadwal_pemeliharaan_berikutnya' => 'date',
        'tanggal_akhir_asuransi'         => 'date',
    ];

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'id_master_barang', 'id_master_barang');
    }

    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi', 'id_kondisi');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'id_ruang', 'id_ruang');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi', 'id_lokasi');
    }
}
