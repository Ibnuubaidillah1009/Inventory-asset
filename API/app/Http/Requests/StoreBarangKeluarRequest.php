<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangKeluarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_keluar' => 'required|date',
            'kode_inventaris' => 'required|string|exists:pengadaan_habis_pakai,kode_inventaris',
            'jumlah_keluar'  => 'required|integer|min:1',
            'id_ruang'       => 'nullable|integer|exists:ruang,id_ruang',
            'nama_penerima'  => 'required|integer|exists:pengguna,id_pengguna',
            'petugas'        => 'required|integer|exists:pengguna,id_pengguna',
            'kode_gudang'    => 'required|string|exists:gudang,kode_gudang',
            'keterangan'     => 'nullable|string',
        ];
    }
}
