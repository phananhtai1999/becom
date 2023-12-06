<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckUserUuidAndAppId
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->appId() && auth()->user()) {

            return $next($request);
        }

        return response()->json([
            'status' => false,
            'locale' => app()->getLocale(),
            'message' => __('messages.unauthorized')
        ], 403);
    }
}
