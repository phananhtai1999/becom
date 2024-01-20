<?php

namespace App\Http\Middleware;

use App\Models\Scopes\AppIdScope;
use Closure;
use Illuminate\Http\Request;

class ExcludeAppIdScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        AppIdScope::$isEnabled = false;

        return $next($request);
    }
}
