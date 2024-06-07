<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogErrorResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if the response is an error response (status code >= 400)
        if ($response->isServerError() || $response->isClientError()) {
            Log::error('Error Response: ' . $response->getContent());
        }

        return $response;
    }
}
