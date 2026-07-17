<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMutasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_jurusan_asal'   => ['nullable', 'integer', 'exists:jurusan,id_jurusan'],
            'id_jurusan_tujuan' => ['required', 'integer', 'exists:jurusan,id_jurusan'],
            'tanggal_mutasi'    => ['required', 'date'],
            'kode_inventaris'   => ['required', 'string'],
            'alasan_mutasi'     => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_jurusan_asal.exists'    => 'Jurusan asal tidak ditemukan.',
            'id_jurusan_tujuan.required'=> 'Jurusan tujuan wajib diisi.',
            'id_jurusan_tujuan.exists'  => 'Jurusan tujuan tidak ditemukan.',
            'tanggal_mutasi.required'   => 'Tanggal mutasi wajib diisi.',
            'tanggal_mutasi.date'       => 'Format tanggal mutasi tidak valid.',
            'kode_inventaris'           => 'Kode inventaris wajib diisi.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
