<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAksesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_modul' => ['required', 'string', 'max:100', 'unique:akses,nama_modul'],
            'hak_buat'   => ['required', 'boolean'],
            'hak_baca'   => ['required', 'boolean'],
            'hak_ubah'   => ['required', 'boolean'],
            'hak_hapus'  => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_modul.required' => 'Nama modul wajib diisi.',
            'nama_modul.unique'   => 'Nama modul sudah ada.',
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
