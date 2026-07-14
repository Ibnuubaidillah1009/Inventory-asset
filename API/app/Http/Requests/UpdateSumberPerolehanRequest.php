<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSumberPerolehanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('sumberPerolehan');

        return [
            'kode_sumber' => ['sometimes', 'required', 'string', 'max:50', "unique:sumber_perolehan,kode_sumber,{$id},id_sumber_perolehan"],
            'nama'        => ['sometimes', 'required', 'string', 'max:100'],
            'keterangan'  => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
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
