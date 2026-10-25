<?php

namespace App\Infrastructure\Adapters\In\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjuntarSoporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'archivo' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // Máximo 5MB
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'archivo.required' => 'Debe adjuntar un archivo de soporte.',
            'archivo.file' => 'El soporte debe ser un archivo válido.',
            'archivo.mimes' => 'El archivo debe estar en formato PDF, JPG, JPEG o PNG.',
            'archivo.max' => 'El tamaño del archivo no puede superar los 5MB.',
        ];
    }
}
