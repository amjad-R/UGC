<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeen
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $profile = Auth::user()->profile;
            if ($profile) {
                $profile->is_online = false;
                $profile->last_seen = now();
                $profile->save();
            }
        }

        return $next($request);
    }
}
