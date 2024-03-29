<?php

namespace App\Guards;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class HeaderGuard implements Guard
{
    protected $request;
    protected $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = new User(["uuid" => $this->userId()]);
    }

    public function user()
    {
        return $this->user;
    }

    public function userId()
    {
        // Get value 'x-user-id' to header
        return $this->request->header('x-user-id');
    }

    public function appId()
    {
        // Get value 'x-api-key' to header
        return $this->request->header('x-app-id');
    }

    public function apps()
    {
        // Get value 'x-api-key' to header
        return ['sem2'];
    }

    public function apiKey()
    {
        // Get value 'x-api-key' to header
        return $this->request->header('x-api-key');
    }

    public function token()
    {
        // Get value 'x-token' to header
        return $this->request->header('x-token');
    }

    public function roles()
    {
        try {
            $decodedToken = JWT::decode(auth()->token(), new Key(config('api_base.token_key'), 'HS256'));
            if (!optional($decodedToken->data)->roles) {
                return collect([]);
            }

            return collect($decodedToken->data->roles);
        } catch (SignatureInvalidException|\InvalidArgumentException|\ErrorException|\TypeError|\UnexpectedValueException $exception) {
            return collect([]);
        }
    }

    public function hasRole($roles)
    {
        $intersection = $this->roles()->intersect($roles);

        return $intersection->isNotEmpty();
    }

    public function check()
    {
        // TODO: Implement check() method.
    }

    public function guest()
    {
        // TODO: Implement guest() method.
    }

    public function id()
    {
        // TODO: Implement id() method.
    }

    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }

    public function hasUser()
    {
        // TODO: Implement hasUser() method.
    }

    public function setUser(Authenticatable $user)
    {
        // TODO: Implement setUser() method.
    }
}

