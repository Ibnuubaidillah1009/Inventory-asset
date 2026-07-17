<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBarangNonAktifRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_inventaris'  => ['required', 'string', 'max:50', 'exists:aset,kode_inventaris'],
            'id_status'        => ['nullable', 'integer', 'exists:status_barang,id_status'],
            'jumlah_nonaktif'  => ['nullable', 'integer', 'min:1'],
            'tanggal'          => ['required', 'date'],
            'keterangan'       => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422));
    }
}
