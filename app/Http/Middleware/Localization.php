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
        $lang = $request->cookie('lang') ? $request->cookie('lang') : 'en';

        //Set laravel localization
        app()->setLocale($lang);

        if($request->get('lang'))
        {
            app()->setLocale($request->get('lang'));

            return $next($request);
        }

        return $next($request)->withCookie(cookie('lang' , $lang, 3600, null, null, true, false));
    }
}
