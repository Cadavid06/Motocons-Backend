<?php

namespace App\Infrastructure\Adapters\In\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IniciarSolicitudCreditoRequest extends FormRequest
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
            'cliente_tipo_doc' => ['required', 'string', 'max:10'],
            'cliente_num_doc' => ['required', 'string', 'max:20'],
            'medio_pago' => ['required', 'string', 'in:BRILLA,SERVICREDITO,brilla,servicredito'],
            'venta_numero' => ['nullable', 'string', 'max:50'],
            'vin_moto' => ['nullable', 'string', 'max:50'],
            'vendedor_tipo_doc' => ['nullable', 'string', 'max:10'],
            'vendedor_num_doc' => ['nullable', 'string', 'max:20'],
            'canal' => ['nullable', 'string', 'in:PRESENCIAL,WEB,presencial,web'],
            'aceptacion_terminos' => ['nullable', 'date'],
        ];
    }
}
