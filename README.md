# MOTOCONS - Sistema de Gestión de Concesionario y Taller

Sistema integral de gestión comercial, operativa y financiera para el almacén, concesionario y taller de motocicletas **MOTOCONS**, desarrollado con **Laravel 12 / PHP 8.3+** bajo **Arquitectura Hexagonal (Puertos y Adaptadores)** en el backend y arquitectura **MVC** con **Tailwind CSS v4** e iconografía vectorial **Lucide** en la interfaz web, con persistencia en **PostgreSQL (Neon Cloud)**.

---

## Documentación Técnica

* [**Guía de Arquitectura Hexagonal y Casos de Uso**](docs/ARQUITECTURA_HEXAGONAL.md)
* [**Guía de la Capa de Dominio y Sintaxis PHP 8.3+**](docs/GUIA_CAPA_DOMINIO.md)

---

## Módulo de Créditos y Financiación

El sistema cuenta con el flujo completo de estudio y aprobación de créditos para los convenios de **Crédito Brilla** (Gases del Caribe / Surtigas) y **ServiCrédito**:

### 1. Endpoints de la API REST (`routes/api.php`)

| Método | Endpoint | Caso de Uso | Descripción |
| :--- | :--- | :--- | :--- |
| **`GET`** | `/api/creditos/requisitos` | `ListarRequisitosCreditoUseCase` | Consulta catálogo de requisitos con filtro opcional `?medio_pago=BRILLA` o `SERVICREDITO`. |
| **`POST`** | `/api/creditos/solicitudes` | `IniciarSolicitudCreditoUseCase` | Radica la venta en estado `EN_PROCESO_CREDITO`, reserva la motocicleta por VIN y genera los requisitos en `PENDIENTE`. |
| **`POST`** | `/api/creditos/solicitudes/{num}/requisitos/{id}/soporte` | `AdjuntarSoporteRequisitoUseCase` | Carga y archiva el archivo soporte (PDF, JPG, PNG de hasta 5MB). |
| **`PATCH`** | `/api/creditos/solicitudes/{num}/requisitos/{id}/evaluar` | `EvaluarRequisitoCreditoUseCase` | Dictamen del analista de crédito (`VALIDAR` o `RECHAZAR`) registrando auditoría. |
| **`POST`** | `/api/creditos/solicitudes/{num}/aprobar` | `AprobarCreditoUseCase` | Evalúa cumplimiento de requisitos obligatorios y avanza la venta a `PAGADA`. |

### 2. Reglas de Negocio en la Capa de Dominio

* **Invariante de Recibo de Gas:** En créditos Brilla es obligatorio adjuntar y validar el recibo de gas cancelado. De lo contrario, se dispara la excepción de negocio `RequisitoCreditoInvalidoException`.
* **Reserva de Unidad:** Al seleccionar una motocicleta por su VIN, la unidad cambia atómicamente su estado a `RESERVADA` para evitar sobreventa.
* **Inmutabilidad:** Las ventas que hayan sido entregadas o anuladas quedan bloqueadas contra modificaciones (`VentaNoEditableException`).

---

## Interfaz Web de Usuario (MVC)

La capa web (`routes/web.php`) expone una interfaz gráfica con diseño inspirado en aplicaciones de escritorio tipo **JavaFX Modena / Caspian UI**:

* **Ruta Web:** `http://127.0.0.1:8000/` o `http://127.0.0.1:8000/creditos`
* **Estética:** Paleta sobria blanco-gris (`#edf0f5`), barra de título y barra de estado de escritorio, pestañas físicas (`TabPane`), tablas estructuradas e iconografía vectorial SVG oficial de **Lucide** (sin emojis informales).
* **Pestañas funcionales:**
  1. *Iniciar Solicitud:* Radicación de créditos con selección de cliente, motocicleta y financiera.
  2. *Cargar Soportes:* Carga asíncrona de archivos con indicadores de estado.
  3. *Mesa de Control y Aprobación:* Panel de dictamen para analistas y botón de emisión de aprobación final.
  4. *Catálogo de Requisitos:* Matriz paramétrica de documentos por financiera.

---

## Instalación y Puesta en Marcha

### 1. Clonar el repositorio
```bash
git clone https://github.com/Cadavid06/Motocons-Backend.git
cd Motocons-Backend
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar variables de entorno
Copia la plantilla:
```bash
cp .env.example .env
php artisan key:generate
```

Configura tu conexión a PostgreSQL (o utiliza Neon Cloud con SSL):
```env
DB_CONNECTION=pgsql
DB_HOST=ep-nameless-band-b7mawtj7-pooler.c-13.us-east-1.aws.neon.tech;hostaddr=100.62.177.240
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=tu_password
DB_SSLMODE=require
```

### 4. Migraciones y Seeders
Ejecuta las migraciones y puebla los datos iniciales y de demostración:
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Compilar Frontend y Levantar Servidor
```bash
npm run build
php artisan serve
```
La aplicación quedará disponible en `http://127.0.0.1:8000`.

---

## Ejecución de Pruebas Automatizadas

La suite incluye pruebas de integración y características ejecutadas en SQLite in-memory:

```bash
php artisan test
```

---

## Estructura del Código Fuente

```text
app/
├── Domain/                                     # CAPA DE DOMINIO (Núcleo Puro sin ORMs)
│   ├── Model/                                  # Entidades POPO (Venta, Motocicleta, RequisitoCredito, etc.)
│   └── Exceptions/                             # Excepciones de negocio (RequisitoCreditoInvalidoException, etc.)
│
├── Application/                                # CAPA DE APLICACIÓN (Casos de Uso)
│   ├── DTO/                                    # Data Transfer Objects
│   ├── Ports/
│   │   ├── In/                                 # Interfaces de Casos de Uso
│   │   └── Out/                                # Interfaces de Repositorios
│   └── Services/                               # Implementación de Casos de Uso
│
├── Infrastructure/                             # CAPA DE INFRAESTRUCTURA
│   ├── Adapters/
│   │   ├── In/Http/
│   │   │   ├── Controllers/                    # Controladores API REST y Web
│   │   │   └── Requests/                       # FormRequests de validación
│   │   └── Out/Persistence/Eloquent/
│   │       ├── Models/                         # Modelos Eloquent
│   │       ├── Mappers/                        # Mappers Dominio <-> Eloquent
│   │       └── Repositories/                   # Implementación de Ports/Out
│   └── Providers/
│       └── HexagonalServiceProvider.php        # Registro de dependencias IoC
│
└── Shared/                                     # Traits y utilidades compartidas
```

---

## Equipo de Desarrollo
**Ingeniería de Sistemas - Universidad de Córdoba (Sede Sahagún)**
