<?php

namespace App\Http\Middleware;

use Closure;

class TransferAuthDataToBody
{
    public function handle($request, Closure $next)
    {

        if (!$request->get('app_id') && auth()->appId()) {
            $request->merge(['app_id' => auth()->appId()]);
        }
        if (!$request->get('user_id') && auth()->userId()) {
            $request->merge(['user_id' => auth()->userId()]);
        }

        return $next($request);
    }
}
