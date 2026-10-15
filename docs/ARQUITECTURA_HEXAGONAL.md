# 🏛️ Arquitectura Hexagonal en MOTOCONS (Laravel 12 / PHP 8.3+)

Bienvenido a la documentación arquitectónica del backend de **MOTOCONS**. Este proyecto implementa el patrón de **Arquitectura Hexagonal (Puertos y Adaptadores)** para lograr un desacoplamiento total entre las reglas de negocio y los detalles tecnológicos (framework, base de datos, API HTTP, librerías externas).

---

## 🎯 1. Principio Fundamental: Regla de Dependencia

El sistema está dividido en capas concéntricas. La regla de oro es:

> **Las capas internas NUNCA conocen a las capas externas.**
> * El **Dominio** no sabe qué es Laravel, qué es una base de datos ni qué es JSON.
> * La **Aplicación** solo conoce al Dominio y define interfaces (Puertos).
> * La **Infraestructura** conoce a la Aplicación y al Dominio, y se encarga de implementar los detalles técnicos (Eloquent, PostgreSQL, Controladores HTTP).

```
┌─────────────────────────────────────────────────────────────┐
│                       INFRAESTRUCTURA                       │
│  (Laravel Controllers, Eloquent ORM, PostgreSQL, DIAN XML)  │
│                                                             │
│         ┌─────────────────────────────────────────┐         │
│         │               APLICACIÓN                │         │
│         │   (Casos de Uso, DTOs, Puertos In/Out)  │         │
│         │                                         │         │
│         │         ┌─────────────────────┐         │         │
│         │         │       DOMINIO       │         │         │
│         │         │  (Entidades Puras,  │         │         │
│         │         │   Reglas, Excep.)   │         │         │
│         │         └─────────────────────┘         │         │
│         └─────────────────────────────────────────┘         │
└─────────────────────────────────────────────────────────────┘
```

---

## 📂 2. Estructura de Carpetas del Proyecto

El código fuente vive dentro de `app/` estructurado de la siguiente forma:

```text
app/
├── Domain/                                     # 🟢 CAPA DE DOMINIO (Núcleo Puro)
│   ├── Model/                                  # 24 Entidades de Negocio (POPOs puros)
│   ├── Exceptions/                             # Excepciones de negocio (DomainException)
│   ├── ValueObjects/                           # Objetos de Valor inmutables
│   └── Events/                                 # Eventos de dominio
│
├── Application/                                # 🟡 CAPA DE APLICACIÓN (Casos de Uso)
│   ├── DTO/                                    # Data Transfer Objects (Entrada/Salida)
│   ├── Ports/
│   │   ├── In/                                 # Puertos Primarios (Interfaces UseCase)
│   │   └── Out/                                # Puertos Secundarios (Interfaces Repositorios)
│   └── Services/                               # Implementación de los Casos de Uso
│
├── Infrastructure/                             # 🔴 CAPA DE INFRAESTRUCTURA (Detalles Técnicos)
│   ├── Adapters/
│   │   ├── In/
│   │   │   └── Http/
│   │   │       ├── Controllers/                # Controladores API REST de Laravel
│   │   │       └── Requests/                   # FormRequests de validación HTTP
│   │   └── Out/
│   │       └── Persistence/
│   │           └── Eloquent/
│   │               ├── Models/                 # 24 Modelos Eloquent (ORM Entities)
│   │               ├── Mappers/                # Transformadores: EloquentModel <-> DomainModel
│   │               └── Repositories/           # Implementación de Ports/Out usando Eloquent
│   └── Providers/
│       └── HexagonalServiceProvider.php        # Inyección de dependencias (IoC Container)
│
└── Shared/                                     # Utilidades transversales
    └── Traits/
        └── HasCompositePrimaryKey.php          # Soporte para claves compuestas en Eloquent
```

---

## 🔄 3. Flujo de Comunicación (Ejemplo: Registrar una Venta)

Cuando un cliente o vendedor hace una petición a la API, el flujo recorre las capas en este orden estricto:

```
[Cliente HTTP / Postman]
       │
       ▼ (Petición JSON POST /api/ventas)
1. Controller (Infrastructure / Adapter In)
       │ Captura Request y mapea a DTO
       ▼
2. CrearVentaUseCase (Application / Port In)
       │ Invoca el contrato del caso de uso
       ▼
3. CrearVentaService (Application / Service)
       │ Orquesta la lógica llamando a:
       ├──> Entidad Venta (Domain / Model) -> Ejecuta reglas: calcular comisiones, verificar estados.
       └──> VentaRepositoryPort (Application / Port Out) -> Pide persistir la venta.
                                    │
                                    ▼
4. EloquentVentaRepository (Infrastructure / Adapter Out)
       │ Convierte la entidad de Dominio a EloquentModel mediante VentaMapper
       ▼
5. PostgreSQL Database
       │ Almacena en la tabla 'venta'
       ▼
[Retorno]: El resultado sube en reversa transformado en un ResponseDTO hacia el Controller (JSON).
```

---

## 🛠️ 4. Guía para Compañeros: ¿Cómo Desarrollar una Nueva Funcionalidad?

Para aportar al proyecto en las capas de **Aplicación** e **Infraestructura**, sigue estos pasos:

### Paso 1: Definir el DTO (`app/Application/DTO/`)
Crea un objeto inmutable con los datos necesarios para la operación:
```php
namespace App\Application\DTO;

readonly class RegistrarClienteDTO
{
    public function __construct(
        public string $tipoDoc,
        public string $numDoc,
        public string $nombre,
        public ?string $email = null
    ) {}
}
```

### Paso 2: Crear el Puerto de Entrada (`app/Application/Ports/In/`)
Define el contrato del caso de uso:
```php
namespace App\Application\Ports\In;

use App\Application\DTO\RegistrarClienteDTO;
use App\Domain\Model\Cliente;

interface RegistrarClienteUseCase
{
    public function execute(RegistrarClienteDTO $dto): Cliente;
}
```

### Paso 3: Crear el Puerto de Salida (`app/Application/Ports/Out/`)
Define el contrato que necesitarás para acceder a la base de datos:
```php
namespace App\Application\Ports\Out;

use App\Domain\Model\Cliente;

interface ClienteRepositoryPort
{
    public function guardar(Cliente $cliente): Cliente;
    public function buscarPorDocumento(string $tipoDoc, string $numDoc): ?Cliente;
}
```

### Paso 4: Implementar el Caso de Uso (`app/Application/Services/`)
Implementa la lógica orquestadora inyectando el puerto de salida por constructor:
```php
namespace App\Application\Services;

use App\Application\Ports\In\RegistrarClienteUseCase;
use App\Application\Ports\Out\ClienteRepositoryPort;
use App\Application\DTO\RegistrarClienteDTO;
use App\Domain\Model\Cliente;

class RegistrarClienteService implements RegistrarClienteUseCase
{
    public function __construct(
        private ClienteRepositoryPort $clienteRepository
    ) {}

    public function execute(RegistrarClienteDTO $dto): Cliente
    {
        $cliente = new Cliente($dto->tipoDoc, $dto->numDoc, $dto->nombre, $dto->email);
        return $this->clienteRepository->guardar($cliente);
    }
}
```

### Paso 5: Implementar el Adaptador de Salida (`app/Infrastructure/Adapters/Out/Persistence/Eloquent/Repositories/`)
Implementa el repositorio usando el modelo Eloquent y el mapper:
```php
namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories;

use App\Application\Ports\Out\ClienteRepositoryPort;
use App\Domain\Model\Cliente;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Cliente as ClienteEloquent;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers\ClienteMapper;

class EloquentClienteRepository implements ClienteRepositoryPort
{
    public function guardar(Cliente $cliente): Cliente
    {
        $eloquentModel = ClienteMapper::toEloquent($cliente);
        $eloquentModel->save();
        return ClienteMapper::toDomain($eloquentModel);
    }
}
```

### Paso 6: Vincular en el Service Provider (`app/Infrastructure/Providers/HexagonalServiceProvider.php`)
Configura la inyección de dependencias en Laravel (respetando la regla de **cero singletons**):
```php
$this->app->bind(ClienteRepositoryPort::class, EloquentClienteRepository::class);
$this->app->bind(RegistrarClienteUseCase::class, RegistrarClienteService::class);
```

### Paso 7: Crear el Controlador HTTP (`app/Infrastructure/Adapters/In/Http/Controllers/`)
Inyecta el caso de uso y expón el endpoint en `routes/api.php`:
```php
class ClienteController
{
    public function __construct(
        private RegistrarClienteUseCase $registrarClienteUseCase
    ) {}

    public function store(Request $request): JsonResponse
    {
        $dto = new RegistrarClienteDTO(...$request->all());
        $cliente = $this->registrarClienteUseCase->execute($dto);
        return response()->json($cliente, 201);
    }
}
```