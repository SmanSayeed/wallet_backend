<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class VerifyEmailMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || $request->user() && ! $request->user()->hasVerifiedEmail()) {
            return ResponseHelper::error('Email is not verified.', null, 403);
        }

        return $next($request);
    }
}
