<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil id_pengguna dari route parameter
        $id = $this->route('pengguna');

        return [
            'username' => ['sometimes', 'required', 'string', 'max:100', "unique:pengguna,username,{$id},id_pengguna"],
            'full_name'=> ['sometimes', 'required', 'string',],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'id_peran' => ['sometimes', 'required', 'integer', 'exists:peran,id_peran'],
            'id_kelas' => ['nullable', 'integer', 'exists:kelas,id_kelas'],
            'id_mapel' => ['nullable', 'integer', 'exists:mapel,id_mapel'],
            'id_unit'  => ['nullable', 'integer', 'exists:unit,id_unit'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'   => 'Username sudah digunakan.',
            'password.min'      => 'Password minimal 8 karakter.',
            'id_peran.exists'   => 'Peran tidak ditemukan.',
            'id_kelas.exists'   => 'Kelas tidak ditemukan.',
            'id_mapel.exists'   => 'Mapel tidak ditemukan.',
            'id_unit.exists'    => 'Unit tidak ditemukan.',
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
