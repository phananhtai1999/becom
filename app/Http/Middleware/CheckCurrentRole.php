<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckCurrentRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $role
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $currentRoles = auth()->user()->roles;
        $allowedRoles = array_slice(func_get_args(), 2);
        foreach ($currentRoles as $currentRole) {
            if( in_array($currentRole->slug, $allowedRoles) ) {
                return $next($request);
            }
        }

        return response()->json([
            'status' => false,
            'locale' => app()->getLocale(),
            'message' => __('messages.unauthorized')
        ], 403);
    }
}
