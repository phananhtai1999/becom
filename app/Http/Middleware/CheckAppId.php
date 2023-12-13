<?php

namespace App\Http\Middleware;

use App\Abstracts\AbstractRestAPIController;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckAppId extends AbstractRestAPIController
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->appId()) {

            return $next($request);
        }

        return $this->sendUnAuthorizedJsonResponse();
    }
}
