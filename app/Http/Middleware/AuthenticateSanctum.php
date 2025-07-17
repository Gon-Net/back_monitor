<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
class AuthenticateSanctum extends Middleware
{
    static function no_logged(){
        return response()->json([
            'message' => 'Token inválido, expirado o no proporcionado',
            'error' => 'Unauthenticated'
        ], 401);
    }
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            return $this->no_logged();
        }

        return $next($request);
    }
}
