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
            $decodedToken = JWT::decode(auth()->token(), new Key(config('api_base.token_key'), 'HS256'));
            $currentRoles = optional($decodedToken->data)->roles;
            $allowedRoles = array_slice(func_get_args(), 2);
            foreach ($currentRoles as $currentRole) {
                if (in_array($currentRole, $allowedRoles)) {
                    return $next($request);
                }
            }

            return $this->sendUnAuthorizedJsonResponse();
        } catch (SignatureInvalidException|\InvalidArgumentException|\ErrorException|\TypeError|\UnexpectedValueException $exception) {
            return $this->sendInternalServerErrorJsonResponse();
        }
    }
}
