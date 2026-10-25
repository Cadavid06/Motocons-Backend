<?php

namespace App\Application\Services;

use App\Application\DTO\IniciarSolicitudCreditoDTO;
use App\Application\DTO\SolicitudCreditoResponseDTO;
use App\Application\Ports\In\IniciarSolicitudCreditoUseCase;
use App\Application\Ports\Out\MotocicletaRepositoryPort;
use App\Application\Ports\Out\RequisitoCreditoRepositoryPort;
use App\Application\Ports\Out\VentaRepositoryPort;
use App\Application\Ports\Out\VentaRequisitoRepositoryPort;
use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\VentaNoEditableException;
use App\Domain\Model\Venta;
use App\Domain\Model\VentaRequisito;
use DateTimeImmutable;

class IniciarSolicitudCreditoService implements IniciarSolicitudCreditoUseCase
{
    public function __construct(
        private VentaRepositoryPort $ventaRepository,
        private VentaRequisitoRepositoryPort $ventaRequisitoRepository,
        private RequisitoCreditoRepositoryPort $requisitoCreditoRepository,
        private MotocicletaRepositoryPort $motocicletaRepository
    ) {}

    public function execute(IniciarSolicitudCreditoDTO $dto): SolicitudCreditoResponseDTO
    {
        // 1. Obtener los requisitos configurados para la financiera
        $requisitosFinanciera = $this->requisitoCreditoRepository->buscarPorMedioPago($dto->medioPago);
        if (empty($requisitosFinanciera)) {
            throw new DomainException("No existen requisitos de crédito configurados para el medio de pago: {$dto->medioPago}");
        }

        // 2. Resolver la Venta (existente o nueva)
        if ($dto->ventaNumero !== null && trim($dto->ventaNumero) !== '') {
            $venta = $this->ventaRepository->buscarPorNumero($dto->ventaNumero);
            if (! $venta) {
                throw new DomainException("La venta con número {$dto->ventaNumero} no existe.");
            }
            if (! $venta->puedeModificarse()) {
                throw VentaNoEditableException::estadoNoPermiteCambios($venta->getNumero(), $venta->getEstado());
            }
            $venta->iniciarProcesoCredito();
        } else {
            $nuevoNumero = $this->ventaRepository->generarSiguienteNumero();
            $venta = new Venta(
                numero: $nuevoNumero,
                fecha: new DateTimeImmutable,
                canal: $dto->canal,
                estado: 'EN_PROCESO_CREDITO',
                clienteTipoDoc: $dto->clienteTipoDoc,
                clienteNumDoc: $dto->clienteNumDoc,
                vendedorTipoDoc: $dto->vendedorTipoDoc,
                vendedorNumDoc: $dto->vendedorNumDoc,
                aceptacionTerminos: $dto->aceptacionTerminos
            );
        }

        // 3. Guardar Venta primero (para que exista la clave primaria para FKs)
        $venta = $this->ventaRepository->guardar($venta);

        // 4. Asociar Motocicleta si se suministró VIN
        $motoData = null;
        if ($dto->vinMoto !== null && trim($dto->vinMoto) !== '') {
            $moto = $this->motocicletaRepository->buscarPorVin($dto->vinMoto);
            if (! $moto) {
                throw new DomainException("La motocicleta con VIN {$dto->vinMoto} no existe.");
            }
            $moto->reservarParaVenta($venta->getNumero());
            $this->motocicletaRepository->guardar($moto);
            $venta->asociarMotocicleta($moto);

            $motoData = [
                'vin' => $moto->getVin(),
                'modelo' => $moto->getModeloReferencia(),
                'color' => $moto->getColor(),
                'precio_lista' => $moto->getPrecioLista(),
                'descuento' => $moto->getDescuento(),
                'precio_final' => $moto->getPrecioFinal(),
            ];
        }

        // 5. Crear e insertar VentaRequisitos para esta venta
        $requisitosCreados = [];
        $requisitosData = [];
        foreach ($requisitosFinanciera as $req) {
            $vr = new VentaRequisito(
                ventaNumero: $venta->getNumero(),
                requisitoId: $req->getId(),
                estado: 'PENDIENTE'
            );
            $requisitosCreados[] = $vr;
            $venta->asociarRequisito($vr);

            $requisitosData[] = [
                'requisito_id' => $req->getId(),
                'medio_pago' => $req->getMedioPago(),
                'descripcion' => $req->getDescripcion(),
                'obligatorio' => $req->isObligatorio(),
                'estado' => $vr->getEstado(),
                'soporte_adjunto' => $vr->getSoporteAdjunto(),
                'validado_por' => $vr->getValidadoPor(),
            ];
        }

        $this->ventaRequisitoRepository->guardarMuchos($requisitosCreados);

        return new SolicitudCreditoResponseDTO(
            ventaNumero: $venta->getNumero(),
            fecha: $venta->getFecha()->format('Y-m-d H:i:s'),
            canal: $venta->getCanal(),
            estado: $venta->getEstado(),
            clienteTipoDoc: $venta->getClienteTipoDoc(),
            clienteNumDoc: $venta->getClienteNumDoc(),
            medioPago: strtoupper($dto->medioPago),
            requisitos: $requisitosData,
            motocicleta: $motoData
        );
    }
}
