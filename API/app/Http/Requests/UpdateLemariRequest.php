<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLemariRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('lemari');

        return [
            'kode_lemari' => ['sometimes', 'required', 'string', 'max:50', "unique:lemari,kode_lemari,{$id},id_lemari"],
            'nama'        => ['sometimes', 'required', 'string', 'max:100'],
            'id_ruang'    => ['sometimes', 'required', 'integer', 'exists:ruang,id_ruang'],
            'nomor_rak'   => ['nullable', 'string', 'max:50'],
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
