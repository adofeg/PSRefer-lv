<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAssociate
{
    /**
     * Handle an incoming request.
     * Solo verifica la relación polimórfica (que sea Associate).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->isAssociate()) {
            abort(403, 'Acceso denegado. Solo asociados pueden acceder.');
        }

        return $next($request);
    }
}
