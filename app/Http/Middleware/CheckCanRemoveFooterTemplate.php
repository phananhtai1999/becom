<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCanRemoveFooterTemplate
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->can_remove_footer_template) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'locale' => app()->getLocale(),
            'message' => __('messages.unauthorized')
        ], 403);
    }
}
