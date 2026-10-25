<?php

namespace App\Infrastructure\Providers;

use App\Application\Ports\In\AdjuntarSoporteRequisitoUseCase;
use App\Application\Ports\In\IniciarSolicitudCreditoUseCase;
use App\Application\Ports\In\ListarRequisitosCreditoUseCase;
use App\Application\Ports\Out\MotocicletaRepositoryPort;
use App\Application\Ports\Out\RequisitoCreditoRepositoryPort;
use App\Application\Ports\Out\VentaRepositoryPort;
use App\Application\Ports\Out\VentaRequisitoRepositoryPort;
use App\Application\Services\AdjuntarSoporteRequisitoService;
use App\Application\Services\IniciarSolicitudCreditoService;
use App\Application\Services\ListarRequisitosCreditoService;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories\EloquentMotocicletaRepository;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories\EloquentRequisitoCreditoRepository;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories\EloquentVentaRepository;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories\EloquentVentaRequisitoRepository;
use Illuminate\Support\ServiceProvider;

class HexagonalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Requisitos de Crédito
        $this->app->bind(RequisitoCreditoRepositoryPort::class, EloquentRequisitoCreditoRepository::class);
        $this->app->bind(ListarRequisitosCreditoUseCase::class, ListarRequisitosCreditoService::class);

        // Ventas y Motocicletas
        $this->app->bind(VentaRepositoryPort::class, EloquentVentaRepository::class);
        $this->app->bind(VentaRequisitoRepositoryPort::class, EloquentVentaRequisitoRepository::class);
        $this->app->bind(MotocicletaRepositoryPort::class, EloquentMotocicletaRepository::class);

        // Casos de Uso de Crédito
        $this->app->bind(IniciarSolicitudCreditoUseCase::class, IniciarSolicitudCreditoService::class);
        $this->app->bind(AdjuntarSoporteRequisitoUseCase::class, AdjuntarSoporteRequisitoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
