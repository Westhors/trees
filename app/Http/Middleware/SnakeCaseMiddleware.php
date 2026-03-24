<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SnakeCaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Convert request input keys from CamelCase to snake_case
        $input = $this->recursiveSnakeCase($request->all());
        $request->replace($input);
        return $next($request);



    }
    private function recursiveSnakeCase($data): array
    {
        return collect($data)->map(function ($value, $key) {
            // If the value is an array, recursively convert its keys
            if (is_array($value)) {
                $value = $this->recursiveSnakeCase($value);
            }

            // Convert key from camelCase to snake_case
            return [Str::snake($key) => $value];
        })->collapse()->all();
    }

}
