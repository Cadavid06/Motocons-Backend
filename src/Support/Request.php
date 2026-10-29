<?php

namespace App\Support;

final class Request
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $files
     * @param  array<string, string>  $headers
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query = [],
        public readonly array $body = [],
        public readonly array $files = [],
        public readonly array $headers = [],
        public readonly ?string $rawBody = null
    ) {}

    public static function capture(): self
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $rawBody = file_get_contents('php://input') ?: '';
        $body = [];

        $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');

        if (stripos($contentType, 'application/json') !== false) {
            $decoded = json_decode($rawBody, true);

            if (is_array($decoded)) {
                $body = $decoded;
            }
        } elseif (! empty($_POST)) {
            $body = $_POST;
        }

        if ($method === 'POST') {
            $override = $_POST['_method']
                ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']
                ?? null;

            if (is_string($override) && $override !== '') {
                $method = strtoupper($override);
            }
        }

        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';
        $path = '/' . trim($path, '/');

        $headers = [];

        if (function_exists('getallheaders')) {
            foreach (getallheaders() ?: [] as $name => $value) {
                $headers[strtolower((string) $name)] = (string) $value;
            }
        }

        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $header = strtolower(str_replace('_', '-', substr($name, 5)));
                $headers[$header] ??= (string) $value;
            }
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] ??= (string) $_SERVER['CONTENT_TYPE'];
        }

        return new self(
            method: $method,
            path: $path,
            query: $_GET,
            body: $body,
            files: $_FILES,
            headers: $headers,
            rawBody: $rawBody
        );
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->body)) {
            return $this->body[$key];
        }

        return $this->query[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->body) || array_key_exists($key, $this->query);
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        return is_array($file) ? $file : null;
    }

    public function header(string $name, ?string $default = null): ?string
    {
        return $this->headers[strtolower($name)] ?? $default;
    }

    public function wantsJson(): bool
    {
        $accept = $this->header('accept', '') ?? '';

        return str_starts_with($this->path, '/api/')
            || stripos($accept, 'application/json') !== false;
    }
}
