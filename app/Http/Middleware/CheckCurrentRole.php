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

        foreach ($currentRoles as $currentRole) {
            if ($role == $currentRole->slug) {

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
