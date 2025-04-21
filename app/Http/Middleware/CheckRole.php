<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Обрабатывает входящий запрос.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check() && (auth()->user()->role_id != (int) $role || auth()->user()->role_id !== 1)) {
            abort(403, 'Доступ запрещен!');
        }

        return $next($request);
    }
}
