<?php

namespace App\Http\Middleware;

use App\Abstracts\AbstractRestAPIController;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckCurrentRole extends AbstractRestAPIController
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
        try {
            return $next($request);
        } catch (SignatureInvalidException|\InvalidArgumentException|\ErrorException|\TypeError|\UnexpectedValueException $exception) {
            return $this->sendInternalServerErrorJsonResponse();
        }
    }
}
