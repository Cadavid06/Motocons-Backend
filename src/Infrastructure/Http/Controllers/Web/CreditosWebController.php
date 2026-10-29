<?php

namespace App\Infrastructure\Http\Controllers\Web;

use App\Infrastructure\Persistence\Pdo\ReadModels\CreditosReadRepository;
use App\Support\Csrf;
use App\Support\Request;
use App\Support\Response;
use App\Support\View;

final class CreditosWebController
{
    public function __construct(
        private readonly CreditosReadRepository $creditosReadRepository,
        private readonly View $view
    ) {}

    public function index(Request $request): Response
    {
        $html = $this->view->render('creditos.index', [
            'title' => 'MOTOCONS - Gestión de Créditos',
            'csrfToken' => Csrf::token(),
            'clientes' => $this->creditosReadRepository->clientes(),
            'empleados' => $this->creditosReadRepository->empleados(),
            'motocicletasDisponibles' => $this->creditosReadRepository->motocicletasDisponibles(),
            'requisitosCatalogo' => $this->creditosReadRepository->requisitosCatalogo(),
            'solicitudes' => $this->creditosReadRepository->solicitudes(),
        ], 'layouts.app');

        return Response::html($html);
    }
}
