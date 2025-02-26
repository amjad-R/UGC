<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSupportUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (auth()->user()->user_type !== 'support') {
            return response()->json(['message' => 'Access denied: You are not authorized to view this page'], 403);
        }

        return $next($request);
    }
}
