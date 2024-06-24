<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('EnsureEmailIsVerified: checking user verification status');

        if (!$request->user() || ($request->user() instanceof MustVerifyEmail && !$request->user()->hasVerifiedEmail())) {
            \Log::warning('EnsureEmailIsVerified: user not verified or not logged in');
            // return response()->json(['message' => 'Your email address is not verified.'], 403);
            return ResponseHelper::error('Your email address is not verified.', null, 403);
        }

        return $next($request);
    }
}
