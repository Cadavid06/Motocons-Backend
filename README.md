# MOTOCONS - Versión PHP puro

Conversión del proyecto Laravel **Motocons-app** a **PHP puro**, sin Laravel, sin Composer, sin Blade, sin Eloquent y sin Vite. Se conserva la arquitectura hexagonal (Dominio / Aplicación / Infraestructura), las reglas de negocio y el contrato de la API.

El proyecto Laravel original permanece intacto en `../Motocons-app`.

## Requisitos

- PHP 8.1 o superior (probado con 8.3).
- Extensiones: `pdo`, `pdo_sqlite` y/o `pdo_pgsql`, `fileinfo`, `mbstring`, `session`.
- Servidor web (Apache/Nginx) o el servidor embebido de PHP.

No se necesita Composer ni `npm`.

## Inicio rápido con SQLite

```bash
cp .env.example .env
php database/migrate.php --fresh
php database/seed.php
php -S 127.0.0.1:8000 -t public public/router.php
```

Abrir: `http://127.0.0.1:8000/` o `http://127.0.0.1:8000/creditos`.

## Uso con PostgreSQL

Editar `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=motocons_db
DB_USERNAME=postgres
DB_PASSWORD=tu_password
DB_SSLMODE=prefer
```

Luego:

```bash
php database/migrate.php --fresh
php database/seed.php
php -S 127.0.0.1:8000 -t public public/router.php
```

## Uso con Docker

```bash
docker compose up --build
```

El contenedor aplica el esquema, carga los datos de demostración y levanta la aplicación en `http://127.0.0.1:8000`.

## Pruebas

```bash
php tests/run.php
```

La suite usa SQLite en un archivo temporal, ejecuta el esquema y los seeders en cada prueba y valida el flujo completo: catálogo, radicación, reserva de VIN, carga de soportes, dictamen y aprobación.

Con Docker:

```bash
docker run --rm -v "$PWD":/app -w /app php:8.3-cli php tests/run.php
```

## API REST

| Método | Endpoint | Descripción |
| :--- | :--- | :--- |
| `GET` | `/api/creditos/requisitos` | Catálogo de requisitos. Acepta `?medio_pago=BRILLA` o `SERVICREDITO`. |
| `POST` | `/api/creditos/solicitudes` | Radica la solicitud, crea la venta y los requisitos `PENDIENTE`. |
| `POST` | `/api/creditos/solicitudes/{num}/requisitos/{id}/soporte` | Carga un soporte PDF/JPG/PNG de hasta 5 MB. |
| `PATCH` | `/api/creditos/solicitudes/{num}/requisitos/{id}/evaluar` | Dictamen `VALIDAR` o `RECHAZAR`. |
| `POST` | `/api/creditos/solicitudes/{num}/aprobar` | Aprobación final; la venta pasa a `PAGADA`. |
| `GET` | `/storage/{ruta}` | Descarga los soportes almacenados. |
| `GET` | `/up` | Estado del servicio. |

## Estructura

```text
public/
├── index.php              # Front controller
├── router.php             # Router para php -S
└── .htaccess              # Rewrite para Apache

src/
├── Kernel.php             # Arranque, rutas, CSRF, manejo de errores y DI manual
├── Domain/                # Entidades y excepciones (sin dependencias de framework)
├── Application/           # DTOs, puertos y casos de uso
├── Infrastructure/
│   ├── Http/
│   │   ├── Controllers/   # Controladores API y web
│   │   └── Requests/      # Validación manual (reemplazo de FormRequest)
│   └── Persistence/Pdo/
│       ├── Mappers/       # Dominio <-> filas PDO
│       ├── Repositories/  # Implementación de puertos de salida
│       ├── ReadModels/    # Consultas para la interfaz web
│       ├── Schema.php     # Aplicación de esquemas SQL
│       └── DemoSeeder.php # Datos de demostración
└── Support/               # Autoload, Env, Request, Response, Router, View,
                           # Validator, Storage, Session, Csrf y Database

views/                     # Plantillas PHP planas (reemplazo de Blade)
database/                  # Esquemas SQLite/PostgreSQL, migrate.php y seed.php
tests/run.php              # Suite de pruebas sin framework
```

## Equivalencias Laravel -> PHP puro

| Laravel | PHP puro |
| :--- | :--- |
| `routes/web.php`, `routes/api.php` | Rutas declaradas en `src/Kernel.php` |
| Controladores con `JsonResponse` | Controladores con `App\Support\Response` |
| `FormRequest` | Clases en `src/Infrastructure/Http/Requests` + `App\Support\Validator` |
| Eloquent + migraciones | Repositorios PDO + `database/schema.{sqlite,pgsql}.sql` |
| Seeders | `App\Infrastructure\Persistence\Pdo\DemoSeeder` |
| Blade | Plantillas `.php` en `views/` |
| `Storage::disk('public')` | `App\Support\Storage` + ruta `/storage/{ruta}` |
| CSRF de Laravel | `App\Support\Csrf` con sesión nativa PHP |
| Contenedor IoC | Cableado manual en `Kernel::router()` |
| Vite + Tailwind + Lucide | Tailwind v4 y Lucide por CDN |

## Notas

- La interfaz usa Tailwind CSS v4 y Lucide desde CDN, por lo que necesita conexión a internet para verse con estilos e iconos. El comportamiento y el HTML son los mismos.
- Los archivos se guardan en `storage/app/public/soportes_credito` y se sirven mediante la ruta `/storage/{ruta}` con protección contra path traversal.
- `APP_CSRF=true` protege las peticiones mutantes. El JavaScript envía el token en `X-CSRF-TOKEN`.
- Las contraseñas de `.env` y los archivos cargados no se versionan (`.gitignore`).
