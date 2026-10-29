<?php

namespace App\Support;

use RuntimeException;

final class Storage
{
    private const MIME_EXTENSIONS = [
        'application/pdf' => 'pdf',
        'application/x-pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public function __construct(private readonly string $publicPath) {}

    public static function detectMime(string $path): ?string
    {
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        if (class_exists(\finfo::class)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($path);

            return is_string($mime) ? strtolower($mime) : null;
        }

        $mime = mime_content_type($path);

        return is_string($mime) ? strtolower($mime) : null;
    }

    public static function extensionForMime(?string $mime): ?string
    {
        if ($mime === null) {
            return null;
        }

        return self::MIME_EXTENSIONS[strtolower($mime)] ?? null;
    }

    /**
     * @param  array<string, mixed>  $file
     */
    public function storeUploadedFile(array $file, string $directory = 'soportes_credito'): string
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No fue posible recibir el archivo adjunto.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if ($tmpName === '' || ! is_file($tmpName)) {
            throw new RuntimeException('El archivo temporal del soporte no existe.');
        }

        $mime = self::detectMime($tmpName);
        $extension = self::extensionForMime($mime);

        if ($extension === null) {
            $originalExtension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
            $extension = match ($originalExtension) {
                'pdf' => 'pdf',
                'jpg', 'jpeg' => 'jpg',
                'png' => 'png',
                default => null,
            };
        }

        if ($extension === null) {
            throw new RuntimeException('El tipo de archivo del soporte no es válido.');
        }

        $directory = trim(str_replace('\\', '/', $directory), '/');

        if ($directory === '' || str_contains($directory, '..')) {
            throw new RuntimeException('Directorio de almacenamiento no válido.');
        }

        $targetDirectory = $this->publicPath . '/' . $directory;

        if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0775, true) && ! is_dir($targetDirectory)) {
            throw new RuntimeException('No fue posible crear el directorio de soportes.');
        }

        $name = bin2hex(random_bytes(20)) . '.' . $extension;
        $target = $targetDirectory . '/' . $name;

        $moved = is_uploaded_file($tmpName)
            ? move_uploaded_file($tmpName, $target)
            : @rename($tmpName, $target);

        if (! $moved) {
            $moved = @copy($tmpName, $target);
        }

        if (! $moved) {
            throw new RuntimeException('No fue posible guardar el archivo de soporte.');
        }

        @chmod($target, 0644);

        return $directory . '/' . $name;
    }

    public function path(string $relative): ?string
    {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');

        if ($relative === '' || str_contains($relative, '..')) {
            return null;
        }

        $root = realpath($this->publicPath);

        if ($root === false) {
            return null;
        }

        $path = realpath($root . '/' . $relative);

        if ($path === false || ! is_file($path)) {
            return null;
        }

        if (! str_starts_with($path, $root . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $path;
    }
}
