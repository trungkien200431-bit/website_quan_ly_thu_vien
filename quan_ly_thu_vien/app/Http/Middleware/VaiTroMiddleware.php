<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VaiTroMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ($roles !== [] && ! in_array($user->role, $roles, true))) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
