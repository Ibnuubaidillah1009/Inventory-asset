<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100', 'unique:pengguna,username'],
            'full_name' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'id_peran' => ['required', 'integer', 'exists:peran,id_peran'],
            'id_kelas' => ['nullable', 'integer', 'exists:kelas,id_kelas'],
            'id_mapel' => ['nullable', 'integer', 'exists:mapel,id_mapel'],
            'id_unit'  => ['nullable', 'integer', 'exists:unit,id_unit'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'full_name.required'=> 'Full Name wajib diisi',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'id_peran.required' => 'Peran wajib dipilih.',
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
