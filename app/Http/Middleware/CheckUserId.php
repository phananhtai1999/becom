<?php

namespace App\Http\Middleware;

use App\Abstracts\AbstractRestAPIController;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckUserId extends AbstractRestAPIController
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->userId()) {

            return $next($request);
        }

        return $this->sendUnAuthorizedJsonResponse();
    }
}
