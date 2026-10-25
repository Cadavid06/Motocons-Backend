<?php

namespace App\Infrastructure\Adapters\In\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AprobarCreditoRequest extends FormRequest
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
            'aprobado_por' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'aprobado_por.required' => 'Debe registrar el nombre de usuario del analista o asesor que aprueba el crédito.',
        ];
    }
}
