<?php

namespace App\Application\Services;

use App\Application\DTO\AprobacionCreditoResponseDTO;
use App\Application\DTO\AprobarCreditoDTO;
use App\Application\Ports\In\AprobarCreditoUseCase;
use App\Application\Ports\Out\RequisitoCreditoRepositoryPort;
use App\Application\Ports\Out\VentaRepositoryPort;
use App\Application\Ports\Out\VentaRequisitoRepositoryPort;
use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\RequisitoCreditoInvalidoException;
use DateTimeImmutable;

class AprobarCreditoService implements AprobarCreditoUseCase
{
    public function __construct(
        private VentaRepositoryPort $ventaRepository,
        private VentaRequisitoRepositoryPort $ventaRequisitoRepository,
        private RequisitoCreditoRepositoryPort $requisitoCreditoRepository
    ) {}

    public function execute(AprobarCreditoDTO $dto): AprobacionCreditoResponseDTO
    {
        // 1. Validar que la venta exista
        $venta = $this->ventaRepository->buscarPorNumero($dto->ventaNumero);
        if (! $venta) {
            throw new DomainException("La venta con número {$dto->ventaNumero} no existe.");
        }

        // 2. Validar que la venta esté efectivamente en trámite de crédito
        if (strtoupper($venta->getEstado()) !== 'EN_PROCESO_CREDITO') {
            throw new DomainException("La venta {$dto->ventaNumero} se encuentra en estado '{$venta->getEstado()}' y no puede ser aprobada para crédito.");
        }

        // 3. Obtener los requisitos asociados a la venta
        $requisitosVenta = $this->ventaRequisitoRepository->buscarPorVenta($dto->ventaNumero);
        if (empty($requisitosVenta)) {
            throw new DomainException("La venta {$dto->ventaNumero} no tiene requisitos de crédito asignados.");
        }

        // 4. Evaluar cumplimiento de requisitos obligatorios
        foreach ($requisitosVenta as $vr) {
            $catalogo = $this->requisitoCreditoRepository->buscarPorId($vr->getRequisitoId());
            if ($catalogo && $catalogo->isObligatorio() && ! $vr->estaValidado()) {
                // Regla de negocio explícita: recibo de gas obligatorio para Brilla
                if ($catalogo->esParaBrilla() && stripos($catalogo->getDescripcion(), 'gas') !== false) {
                    throw RequisitoCreditoInvalidoException::reciboGasObligatorio($dto->ventaNumero);
                }

                throw new DomainException("No se puede aprobar el crédito: el requisito obligatorio '{$catalogo->getDescripcion()}' no ha sido validado (estado actual: {$vr->getEstado()}).");
            }
        }

        // 5. Transición de estado de la venta: registrar pago y pasar a 'PAGADA'
        $venta->registrarPago();
        $this->ventaRepository->guardar($venta);

        return new AprobacionCreditoResponseDTO(
            ventaNumero: $venta->getNumero(),
            estado: $venta->getEstado(),
            aprobadoPor: $dto->aprobadoPor,
            fecha: (new DateTimeImmutable)->format('Y-m-d H:i:s'),
            mensaje: 'Crédito aprobado con éxito. La venta ha pasado a estado PAGADA y está lista para facturación y entrega.'
        );
    }
}
