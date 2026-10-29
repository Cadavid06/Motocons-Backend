<?php

namespace App\Support;

final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf_token');

        if (! is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::put('_csrf_token', $token);
        }

        return $token;
    }

    public static function check(?string $token): bool
    {
        if (! is_string($token) || $token === '') {
            return false;
        }

        return hash_equals(self::token(), $token);
    }
}
