<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequestsAndResponses
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $requestData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_data' => $request->all(),
        ];

        $responseData = [
            'status_code' => $response->status(),
            'response_data' => $response->getContent(),
        ];

        Log::info('Request', $requestData);
        Log::info('Response', $responseData);

        return $response;
    }
}
