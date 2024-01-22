<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;

class TransferAuthDataToBody
{
    public function handle($request, Closure $next)
    {
        if (!$request->get('app_id') && auth()->appId()) {
            $request->merge(['app_id' => auth()->appId()]);
        }
        if (!$request->get('user_uuid') && auth()->userId()) {
            //Check role != admin,system
            if (!auth()->hasRole([Role::ROLE_ADMIN, Role::ROLE_ROOT])) {
                $request->merge(['user_uuid' => auth()->userId()]);
            }
        }

        return $next($request);
    }
}
