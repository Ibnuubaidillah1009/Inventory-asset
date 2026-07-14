<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAksesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('akses');

        return [
            'nama_modul' => ['sometimes', 'required', 'string', 'max:100', "unique:akses,nama_modul,{$id},id_akses"],
            'hak_buat'   => ['sometimes', 'boolean'],
            'hak_baca'   => ['sometimes', 'boolean'],
            'hak_ubah'   => ['sometimes', 'boolean'],
            'hak_hapus'  => ['sometimes', 'boolean'],
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
