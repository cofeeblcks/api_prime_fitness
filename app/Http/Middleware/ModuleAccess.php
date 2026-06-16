<?php

namespace App\Http\Middleware;

use App\Constants\ApiStatuses;
use App\Traits\Api\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccess
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!Auth::check()) {
            return $this->errorResponse(
                'Usuario no autenticado',
                ApiStatuses::STATUS_FORBIDDEN
            );
        }

        $user = Auth::user();

        $cacheKey = "user_{$user->id}_module_access_{$module}";

        $hasAccess = Cache::remember($cacheKey, 3600, function () use ($user, $module) {
            return $user->role->modules()
                ->where('route', $module)
                ->exists();
        });

        if (!$hasAccess) {
            return $this->errorResponse(
                'No tienes permisos para acceder a este módulo',
                ApiStatuses::STATUS_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
