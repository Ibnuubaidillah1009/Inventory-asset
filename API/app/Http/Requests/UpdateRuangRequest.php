<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRuangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('ruang');

        return [
            'nama_ruang' => ['sometimes', 'required', 'string', 'max:100', "unique:ruang,nama_ruang,{$id},id_ruang"],
            'id_lokasi'  => ['sometimes', 'required', 'integer', 'exists:lokasi,id_lokasi'],
            'keterangan' => ['nullable', 'string'],
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
