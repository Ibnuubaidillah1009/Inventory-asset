<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tahun_ajaran'     => ['sometimes', 'required', 'string', 'max:20'],
            'semester'         => ['sometimes', 'required', 'string', 'max:20'],
            'is_active'        => ['nullable', 'boolean'],
            'tanggal_mulai'    => ['sometimes', 'required', 'date'],
            'tanggal_selesai'  => ['sometimes', 'required', 'date', 'after:tanggal_mulai'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
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
