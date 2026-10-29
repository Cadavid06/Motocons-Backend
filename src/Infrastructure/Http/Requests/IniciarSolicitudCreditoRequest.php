<?php

namespace App\Infrastructure\Http\Requests;

use App\Support\Request;
use App\Support\Validator;

final class IniciarSolicitudCreditoRequest extends RequestValidator
{
    /**
     * @return array<string, mixed>
     */
    public function validate(Request $request): array
    {
        return Validator::validate(
            $this->data($request),
            [
                'cliente_tipo_doc' => ['required', 'string', 'max:10'],
                'cliente_num_doc' => ['required', 'string', 'max:20'],
                'medio_pago' => ['required', 'string', 'in:BRILLA,SERVICREDITO,brilla,servicredito'],
                'venta_numero' => ['nullable', 'string', 'max:50'],
                'vin_moto' => ['nullable', 'string', 'max:50'],
                'vendedor_tipo_doc' => ['nullable', 'string', 'max:10'],
                'vendedor_num_doc' => ['nullable', 'string', 'max:20'],
                'canal' => ['nullable', 'string', 'in:PRESENCIAL,WEB,presencial,web'],
                'aceptacion_terminos' => ['nullable', 'date'],
            ],
            [
                'cliente_tipo_doc.required' => 'Debe indicar el tipo de documento del cliente.',
                'cliente_num_doc.required' => 'Debe indicar el número de documento del cliente.',
                'medio_pago.required' => 'Debe seleccionar un medio de pago.',
                'medio_pago.in' => 'El medio de pago debe ser BRILLA o SERVICREDITO.',
                'canal.in' => 'El canal debe ser PRESENCIAL o WEB.',
                'aceptacion_terminos.date' => 'La fecha de aceptación de términos no es válida.',
            ],
            $request->files
        );
    }
}
