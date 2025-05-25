<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth('api')->user();
            if ($user && $user->rol === 'fotógrafo') {
                return $next($request);
            } else {
                return response()->json(['message' => 'No autorizado'], 403);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token expirado'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token no proporcionado'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al autenticar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
