<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (auth()->user()->role->tag === 'administrator') return $next($request);
        if (in_array(auth()->user()->role->tag, $roles)) return $next($request);

        if (!$request->is('api/*')) {
            return redirect()->back()->with([
                "status" => __('You do not have permission to peform this action.'),
                "type"   => "danger"
            ]);
        }

        return response()->json([
            'message' => __('You do not have permission to peform this action.')
        ], 403);
    }
}
