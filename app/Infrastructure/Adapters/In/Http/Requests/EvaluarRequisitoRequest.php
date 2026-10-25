<?php

namespace App\Infrastructure\Adapters\In\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluarRequisitoRequest extends FormRequest
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
            'decision' => ['required', 'string', 'in:VALIDAR,RECHAZAR,validar,rechazar,VALIDADO,RECHAZADO'],
            'validador_username' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'decision.required' => 'Debe indicar una decisión para el requisito (VALIDAR o RECHAZAR).',
            'decision.in' => 'La decisión debe ser VALIDAR o RECHAZAR.',
            'validador_username.required' => 'Debe registrar el nombre de usuario de quien evalúa el soporte.',
        ];
    }
}
