<?php

// app/Http/Middleware/CheckTokenExpiry.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->expires_at && now()->greaterThan($token->expires_at)) {
            $token->delete(); // opcional: invalidar token
            return response()->json(['message' => 'Token expired'], 401);
        }

        return $next($request);
    }
}
