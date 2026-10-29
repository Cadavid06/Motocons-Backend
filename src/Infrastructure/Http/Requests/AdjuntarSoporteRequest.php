<?php

namespace App\Infrastructure\Http\Requests;

use App\Support\Request;
use App\Support\Validator;

final class AdjuntarSoporteRequest extends RequestValidator
{
    /**
     * @return array<string, mixed>
     */
    public function validate(Request $request): array
    {
        return Validator::validate(
            $this->data($request),
            [
                'archivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ],
            [
                'archivo.required' => 'Debe adjuntar un archivo de soporte.',
                'archivo.file' => 'El soporte debe ser un archivo válido.',
                'archivo.mimes' => 'El archivo debe estar en formato PDF, JPG, JPEG o PNG.',
                'archivo.max' => 'El tamaño del archivo no puede superar los 5MB.',
            ],
            $request->files
        );
    }
}
