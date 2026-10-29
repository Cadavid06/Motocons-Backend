<?php

namespace App\Support;

final class Autoloader
{
    public static function register(string $sourceDirectory): void
    {
        $sourceDirectory = rtrim($sourceDirectory, DIRECTORY_SEPARATOR);

        spl_autoload_register(static function (string $class) use ($sourceDirectory): void {
            $prefix = 'App\\';

            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $file = $sourceDirectory . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            if (is_file($file)) {
                require $file;
            }
        });
    }
}
