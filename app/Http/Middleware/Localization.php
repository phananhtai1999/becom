<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Check params request and set language defaut
        $lang = $request->get('lang') ? $request->get('lang') : 'en';

        //Set laravel localization
        app()->setLocale($lang);

        return $next($request)->withCookie(cookie('lang' , $lang, 3600, null, null, true, false));
    }
}
