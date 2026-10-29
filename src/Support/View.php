<?php

namespace App\Support;

use RuntimeException;

final class View
{
    public function __construct(private readonly string $basePath) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function render(string $template, array $data = [], ?string $layout = null): string
    {
        $content = $this->renderFile($this->resolve($template), $data);

        if ($layout === null) {
            return $content;
        }

        $data['content'] = $content;

        return $this->renderFile($this->resolve($layout), $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function renderFile(string $file, array $data): string
    {
        $render = static function () use ($file, $data): void {
            extract($data, EXTR_SKIP);
            include $file;
        };

        ob_start();

        try {
            $render();
        } catch (\Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return (string) ob_get_clean();
    }

    private function resolve(string $template): string
    {
        $file = $this->basePath . '/' . str_replace('.', '/', $template) . '.php';

        if (! is_file($file)) {
            throw new RuntimeException("Vista no encontrada: {$template}");
        }

        return $file;
    }
}
