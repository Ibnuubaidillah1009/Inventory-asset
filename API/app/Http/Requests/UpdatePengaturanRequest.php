<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePengaturanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_instansi'      => ['sometimes', 'required', 'string', 'max:255'],
            'alamat_instansi'    => ['nullable', 'string'],
            'wallpaper_aplikasi' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'telpon'             => ['nullable', 'string', 'max:30'],
            'website'            => ['nullable', 'string', 'max:255'],
            'email'              => ['nullable', 'email', 'max:255'],
            'kota'               => ['nullable', 'string', 'max:100'],
            'kepala_sekolah'     => ['nullable', 'string', 'max:255'],
            'NIP'                => ['nullable', 'string', 'max:50'],
            'bagian_inventaris'  => ['nullable', 'string', 'max:255'],
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
