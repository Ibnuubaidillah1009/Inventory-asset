<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTanahNonAktifRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_tanah'  => ['required', 'integer', 'exists:aset_tanah,kode_tanah'],
            'id_status'   => ['required', 'integer', 'exists:status_barang,id_status'],
            'tanggal'     => ['required', 'date'],
            'keterangan'  => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422));
    }
}
