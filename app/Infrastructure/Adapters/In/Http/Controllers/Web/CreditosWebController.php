<?php

namespace App\Infrastructure\Adapters\In\Http\Controllers\Web;

use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Cliente;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Empleado;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Motocicleta;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\RequisitoCredito;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Venta;
use Illuminate\Contracts\View\View;

class CreditosWebController
{
    public function index(): View
    {
        $clientes = Cliente::query()->orderBy('nombre_razon_social')->get();
        $empleados = Empleado::query()->orderBy('nombre')->get();
        $motocicletasDisponibles = Motocicleta::with('modeloMoto')
            ->where('estado', 'DISPONIBLE')
            ->orderBy('modelo_referencia')
            ->get();
        $requisitosCatalogo = RequisitoCredito::query()->orderBy('medio_pago')->orderBy('id')->get();
        $solicitudes = Venta::with(['cliente', 'motocicletas.modeloMoto', 'requisitos.requisitoCredito'])
            ->whereIn('estado', ['COTIZACION', 'EN_PROCESO_CREDITO', 'PAGADA', 'ENTREGADA'])
            ->orderByDesc('fecha')
            ->get();

        return view('creditos.index', compact(
            'clientes',
            'empleados',
            'motocicletasDisponibles',
            'requisitosCatalogo',
            'solicitudes'
        ));
    }
}
