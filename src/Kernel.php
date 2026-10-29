<?php

namespace App;

use App\Application\Services\AdjuntarSoporteRequisitoService;
use App\Application\Services\AprobarCreditoService;
use App\Application\Services\EvaluarRequisitoCreditoService;
use App\Application\Services\IniciarSolicitudCreditoService;
use App\Application\Services\ListarRequisitosCreditoService;
use App\Domain\Exceptions\DomainException;
use App\Infrastructure\Http\Controllers\RequisitoCreditoController;
use App\Infrastructure\Http\Controllers\SolicitudCreditoController;
use App\Infrastructure\Http\Controllers\Web\CreditosWebController;
use App\Infrastructure\Persistence\Pdo\ReadModels\CreditosReadRepository;
use App\Infrastructure\Persistence\Pdo\Repositories\PdoMotocicletaRepository;
use App\Infrastructure\Persistence\Pdo\Repositories\PdoRequisitoCreditoRepository;
use App\Infrastructure\Persistence\Pdo\Repositories\PdoVentaRequisitoRepository;
use App\Infrastructure\Persistence\Pdo\Repositories\PdoVentaRepository;
use App\Support\Csrf;
use App\Support\Database;
use App\Support\Request;
use App\Support\Response;
use App\Support\Router;
use App\Support\Storage;
use App\Support\ValidationException;
use App\Support\View;
use PDO;
use Throwable;

final class Kernel
{
    private ?PDO $pdo = null;

    private ?Router $router = null;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(private readonly array $config)
    {
        date_default_timezone_set((string) ($config['app']['timezone'] ?? 'UTC'));
    }

    /**
     * @return array<string, mixed>
     */
    public function config(): array
    {
        return $this->config;
    }

    public function db(): PDO
    {
        return $this->pdo ??= Database::connect($this->config['db']);
    }

    public function handle(Request $request): Response
    {
        try {
            if ($this->csrfEnabled() && ! in_array($request->method, ['GET', 'HEAD', 'OPTIONS'], true)) {
                $token = $request->header('x-csrf-token') ?? $request->input('_token');

                if (! Csrf::check(is_string($token) ? $token : null)) {
                    return $request->wantsJson()
                        ? Response::json(['message' => 'CSRF token mismatch.'], 419)
                        : Response::html('<h1>419</h1><p>Token CSRF no válido.</p>', 419);
                }
            }

            if ($request->method === 'GET' && str_starts_with($request->path, '/storage/')) {
                return $this->serveStorage(substr($request->path, strlen('/storage/')));
            }

            if ($request->method === 'GET' && $request->path === '/up') {
                return Response::json(['status' => 'ok']);
            }

            $match = $this->router()->match($request);

            if ($match === null) {
                return $request->wantsJson()
                    ? Response::json(['message' => 'Not Found.'], 404)
                    : Response::html('<h1>404</h1><p>Recurso no encontrado.</p>', 404);
            }

            $handler = $match['handler'];

            return $handler($request, ...array_values($match['params']));
        } catch (ValidationException $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'errors' => $e->errors,
            ], 422);
        } catch (DomainException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            if ((bool) ($this->config['app']['debug'] ?? false)) {
                if ($request->wantsJson()) {
                    return Response::json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'exception' => $e::class,
                        'trace' => $e->getTraceAsString(),
                    ], 500);
                }

                return Response::html(
                    '<h1>Error 500</h1><pre>' . e($e->getMessage()) . "\n\n" . e($e->getTraceAsString()) . '</pre>',
                    500
                );
            }

            return $request->wantsJson()
                ? Response::json(['success' => false, 'message' => 'Error interno del servidor.'], 500)
                : Response::html('<h1>500</h1><p>Error interno del servidor.</p>', 500);
        }
    }

    private function router(): Router
    {
        if ($this->router !== null) {
            return $this->router;
        }

        $pdo = $this->db();
        $storage = new Storage((string) $this->config['storage']['public']);

        $requisitoRepository = new PdoRequisitoCreditoRepository($pdo);
        $ventaRepository = new PdoVentaRepository($pdo);
        $ventaRequisitoRepository = new PdoVentaRequisitoRepository($pdo);
        $motocicletaRepository = new PdoMotocicletaRepository($pdo);

        $listarRequisitos = new ListarRequisitosCreditoService($requisitoRepository);
        $iniciarSolicitud = new IniciarSolicitudCreditoService(
            $ventaRepository,
            $ventaRequisitoRepository,
            $requisitoRepository,
            $motocicletaRepository
        );
        $adjuntarSoporte = new AdjuntarSoporteRequisitoService($ventaRepository, $ventaRequisitoRepository);
        $evaluarRequisito = new EvaluarRequisitoCreditoService($ventaRepository, $ventaRequisitoRepository);
        $aprobarCredito = new AprobarCreditoService(
            $ventaRepository,
            $ventaRequisitoRepository,
            $requisitoRepository
        );

        $requisitoController = new RequisitoCreditoController($listarRequisitos);
        $solicitudController = new SolicitudCreditoController(
            $iniciarSolicitud,
            $adjuntarSoporte,
            $evaluarRequisito,
            $aprobarCredito,
            $storage
        );

        $webController = new CreditosWebController(
            new CreditosReadRepository($pdo),
            new View((string) $this->config['root'] . '/views')
        );

        $router = new Router;

        $router->add('GET', '/', static fn (Request $request) => $webController->index($request));
        $router->add('GET', '/creditos', static fn (Request $request) => $webController->index($request));

        $router->add('GET', '/api/creditos/requisitos', static fn (Request $request) => $requisitoController->index($request));

        $router->add('POST', '/api/creditos/solicitudes', static fn (Request $request) => $solicitudController->store($request));

        $router->add(
            'POST',
            '/api/creditos/solicitudes/{ventaNumero}/requisitos/{requisitoId}/soporte',
            static fn (Request $request, string $ventaNumero, string $requisitoId) => $solicitudController->adjuntarSoporte($ventaNumero, $requisitoId, $request)
        );

        $router->add(
            'PATCH',
            '/api/creditos/solicitudes/{ventaNumero}/requisitos/{requisitoId}/evaluar',
            static fn (Request $request, string $ventaNumero, string $requisitoId) => $solicitudController->evaluarRequisito($ventaNumero, $requisitoId, $request)
        );

        $router->add(
            'POST',
            '/api/creditos/solicitudes/{ventaNumero}/aprobar',
            static fn (Request $request, string $ventaNumero) => $solicitudController->aprobar($ventaNumero, $request)
        );

        return $this->router = $router;
    }

    private function serveStorage(string $relative): Response
    {
        $storage = new Storage((string) $this->config['storage']['public']);
        $path = $storage->path($relative);

        if ($path === null) {
            return Response::html('<h1>404</h1><p>Archivo no encontrado.</p>', 404);
        }

        $mime = Storage::detectMime($path) ?? 'application/octet-stream';

        return new Response(200, [
            'Content-Type' => $mime,
            'Content-Length' => (string) filesize($path),
        ], (string) file_get_contents($path));
    }

    private function csrfEnabled(): bool
    {
        return (bool) ($this->config['app']['csrf'] ?? true);
    }
}
