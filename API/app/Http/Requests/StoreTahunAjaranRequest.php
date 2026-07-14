<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tahun_ajaran'     => ['required', 'string', 'max:20'],
            'semester'         => ['required', 'string', 'max:20'],
            'is_active'        => ['nullable', 'boolean'],
            'tanggal_mulai'    => ['required', 'date'],
            'tanggal_selesai'  => ['required', 'date', 'after:tanggal_mulai'],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran.required'     => 'Tahun ajaran wajib diisi.',
            'semester.required'         => 'Semester wajib diisi.',
            'tanggal_mulai.required'    => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required'  => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after'     => 'Tanggal selesai harus setelah tanggal mulai.',
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
