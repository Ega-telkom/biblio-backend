<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSubscribed
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->is_subscribed) {
            return response()->json([
                'message' => 'Subscription required',
            ], 403);
        }

        return $next($request);
    }
}
