<?php

namespace App\Support;

final class Response
{
    /**
     * @param  array<string, string>  $headers
     */
    public function __construct(
        public int $status = 200,
        public array $headers = [],
        public string $body = ''
    ) {}

    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        $payload = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );

        if ($payload === false) {
            $status = 500;
            $payload = '{"success":false,"message":"No fue posible serializar la respuesta."}';
        }

        return new self($status, $headers + [
            'Content-Type' => 'application/json; charset=UTF-8',
        ], $payload);
    }

    public static function html(string $html, int $status = 200): self
    {
        return new self($status, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ], $html);
    }

    public static function text(string $text, int $status = 200): self
    {
        return new self($status, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ], $text);
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self($status, ['Location' => $location], '');
    }

    public function send(): void
    {
        if (! headers_sent()) {
            http_response_code($this->status);

            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        echo $this->body;
    }
}
