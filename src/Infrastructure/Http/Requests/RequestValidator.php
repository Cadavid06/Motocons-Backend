<?php

namespace App\Infrastructure\Http\Requests;

use App\Support\Request;

abstract class RequestValidator
{
    /**
     * @return array<string, mixed>
     */
    protected function data(Request $request): array
    {
        return array_merge($request->query, $request->body);
    }
}
