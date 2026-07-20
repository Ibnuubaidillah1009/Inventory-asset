<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengadaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pengadaan'       => $this->id_pengadaan,
            'nomor_po'           => $this->nomor_po,
            'nomor_faktur'       => $this->nomor_faktur,
            'tanggal_pengadaan'  => $this->tanggal_pengadaan?->format('Y-m-d'),
            'id_pemasok'         => $this->id_pemasok,
            'total_harga'        => $this->total_harga,
            'persentase_ppn'     => $this->persentase_ppn,
            'nominal_ppn'        => $this->nominal_ppn,
            'grand_total'        => $this->grand_total,
            'keterangan'         => $this->keterangan,
            'kode_gudang'        => $this->kode_gudang,
            'jumlah_pengadaan'   => $this->jumlah_pengadaan,
            'id_kondisi'         => $this->id_kondisi,
            'id_sumber_perolehan'   => $this->id_sumber_perolehan,
            'sumber_perolehan'     => $this->whenLoaded('sumberPerolehan'),
            'tanggal_pengiriman' => $this->tanggal_pengiriman?->format('Y-m-d'),
            'nomor_po_lampiran'  => $this->nomor_po_lampiran,
            'status'             => $this->status,
            'pemasok'            => $this->whenLoaded('pemasok'),
            'gudang'             => $this->whenLoaded('gudang'),
            'kondisi'            => $this->whenLoaded('kondisi'),
            'permintaan'         => $this->whenLoaded('permintaan'),
            'detail_pengadaan'   => $this->whenLoaded('detailPengadaan', function () {
                return $this->detailPengadaan->map(function ($detail) {
                    return [
                        'id_detail_pengadaan' => $detail->id_detail_pengadaan,
                        'id_master_barang'    => $detail->id_master_barang,
                        'jumlah_masuk'        => $detail->jumlah_masuk,
                        'harga_satuan'        => $detail->harga_satuan,
                        'master_barang'       => $detail->relationLoaded('masterBarang') && $detail->masterBarang ? new MasterBarangResource($detail->masterBarang) : null,
                    ];
                });
            }),
            'aset' => $this->whenLoaded('aset'),
        ];
    }
}
