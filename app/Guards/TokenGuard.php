<?php

namespace App\Guards;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Services\UserAccessTokenService;
use App\Models\User;
use App\Services\UserService;

class TokenGuard implements StatefulGuard
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var User
     */
    private $user;

    /**
     * @var UserAccessTokenService
     */
    private $userAccessTokenServices;

    /**
     * @var UserProvider
     */
    private $provider;

    /**
     * @var UserService
     */
    private $userServices;

    /**
     * TokenGuard constructor.
     * @param UserProvider $provider
     * @param Request $request
     * @param UserAccessTokenService $userAccessTokenServices
     * @param UserService $userServices
     */
    public function __construct(
        UserProvider $provider,
        Request $request,
        UserAccessTokenService $userAccessTokenServices,
        UserService $userServices
    )
    {
        $this->request = $request;
        $this->user = null;
        $this->provider = $provider;
        $this->userAccessTokenServices = $userAccessTokenServices;
        $this->userServices = $userServices;
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        if (empty(auth()->user()) && !empty($this->getToken())) {
            return $this->isTrueToken();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function guest(): bool
    {
        if (!empty(auth()->user()) && !empty($this->getToken())) {
            return !$this->isTrueToken();
        }

        return true;
    }

    /**
     * @return Authenticatable|User|null
     */
    public function user()
    {
        if (!empty($this->user)) {
            return $this->user;
        }

        return null;
    }

    public function id()
    {
        $user = auth()->user();

        if (!empty($user)) {
            return $user->getAuthIdentifier();
        }
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        //
    }

    /**
     * @param Authenticatable $user
     * @return $this
     */
    public function setUser(Authenticatable $user): TokenGuard
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get Token
     */
    public function getToken()
    {
        return $this->request->cookie('accessToken');
    }

    /**
     * @return bool
     */
    public function isTrueToken(): bool
    {
        $user = $this->userServices
            ->findOneById(
                $this->userAccessTokenServices->getCurrentUserKey()
            );

        if ($user) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * @param array $credentials
     * @param false $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false): bool
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials(['email' => $credentials['email']]);

        return Hash::check($credentials['password'], $user->password);
    }

    public function once(array $credentials = [])
    {
        // TODO: Implement once() method.
    }

    public function login(Authenticatable $user, $remember = false)
    {
        // TODO: Implement login() method.
    }

    public function loginUsingId($id, $remember = false)
    {
        // TODO: Implement loginUsingId() method.
    }

    public function onceUsingId($id)
    {
        // TODO: Implement onceUsingId() method.
    }

    public function viaRemember()
    {
        // TODO: Implement viaRemember() method.
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function logout(): bool
    {
        $token = $this->getToken();

        if ($token) {
            $this->userAccessTokenServices->destroy($token);

            Session::flush();
            Cookie::forget('accessToken');
            Cookie::forget('logged');

            return true;
        }

        return false;
    }
}
