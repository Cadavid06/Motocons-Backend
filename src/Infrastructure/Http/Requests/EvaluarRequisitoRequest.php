<?php

namespace App\Infrastructure\Http\Requests;

use App\Support\Request;
use App\Support\Validator;

final class EvaluarRequisitoRequest extends RequestValidator
{
    /**
     * @return array<string, mixed>
     */
    public function validate(Request $request): array
    {
        return Validator::validate(
            $this->data($request),
            [
                'decision' => ['required', 'string', 'in:VALIDAR,RECHAZAR,validar,rechazar,VALIDADO,RECHAZADO'],
                'validador_username' => ['required', 'string', 'max:50'],
            ],
            [
                'decision.required' => 'Debe indicar una decisión para el requisito (VALIDAR o RECHAZAR).',
                'decision.in' => 'La decisión debe ser VALIDAR o RECHAZAR.',
                'validador_username.required' => 'Debe registrar el nombre de usuario de quien evalúa el soporte.',
            ],
            $request->files
        );
    }
}
