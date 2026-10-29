<?php

namespace App\Infrastructure\Http\Requests;

use App\Support\Request;
use App\Support\Validator;

final class AprobarCreditoRequest extends RequestValidator
{
    /**
     * @return array<string, mixed>
     */
    public function validate(Request $request): array
    {
        return Validator::validate(
            $this->data($request),
            [
                'aprobado_por' => ['required', 'string', 'max:50'],
            ],
            [
                'aprobado_por.required' => 'Debe registrar el nombre de usuario del analista o asesor que aprueba el crédito.',
            ],
            $request->files
        );
    }
}
