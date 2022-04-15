<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSocialProfileCollection;
use App\Http\Resources\UserSocialProfileResource;
use App\Services\AuthenticationService;
use App\Services\UserAccessTokenService;
use App\Services\UserService;
use App\Services\UserSocialProfileService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthBySocialNetworkController extends AbstractRestAPIController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var UserAccessTokenService
     */
    private $userAccessTokenService;

    /**
     * @param AuthenticationService $authenticationService
     * @param UserAccessTokenService $userAccessTokenService
     * @param UserSocialProfileService $service
     * @param UserService $userService
     */
    public function __construct(
        AuthenticationService $authenticationService,
        UserAccessTokenService $userAccessTokenService,
        UserSocialProfileService $service,
        UserService $userService
    )
    {
        $this->authenticationService = $authenticationService;
        $this->userAccessTokenService = $userAccessTokenService;
        $this->service = $service;
        $this->userService = $userService;
        $this->resourceCollectionClass = UserSocialProfileCollection::class;
        $this->resourceClass = UserSocialProfileResource::class;
    }

    /**
     * @param $driver
     * @return JsonResponse|void
     */
    public function loginUrl($driver)
    {
        if ($driver == 'google') {
            return $this->sendOkJsonResponse([
                'data' => ['redirect_url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()]
            ]);
        } elseif ($driver == 'facebook') {
            return $this->sendOkJsonResponse([
                'data' => ['redirect_url' => Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl()]
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function loginByGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $findUserSocial = $this->service->findBySocialProfileEmail('google_' . $googleUser->getId());

        $findUser = $this->userService->findByEmail($googleUser->email);

        if (!empty($findUserSocial && $findUser)) {

            return $this->loginSocial($findUser);
        } elseif (!empty($findUser) && empty($findUserSocial)) {
            $this->service->create([
                'social_profile_key' => 'google_' . $googleUser->getId(),
                'social_network_uuid' => 'google',
                'user_uuid' => $findUser->uuid,
                'social_profile_name' => $googleUser->getName(),
                'social_profile_avatar' => $googleUser->avatar,
                'social_profile_email' => $googleUser->email,
                'updated_info_at' => Carbon::now(),
            ]);

            return $this->loginSocial($findUser);
        } else {
            $newUser = $this->userService->create([
                'email' => $googleUser->email,
                'username' => $googleUser->email,
                'password' => Hash::make(Str::random(20)),
            ]);

            $newUser->roles()->attach([config('user.default_role_uuid')]);

            $this->service->create([
                'social_profile_key' => 'google_' . $googleUser->getId(),
                'social_network_uuid' => 'google',
                'user_uuid' => $newUser->uuid,
                'social_profile_name' => $googleUser->getName(),
                'social_profile_avatar' => $googleUser->avatar,
                'social_profile_email' => $googleUser->email,
                'updated_info_at' => Carbon::now(),
            ]);

            return $this->loginSocial($newUser);
        }
    }

    /**
     * @return JsonResponse
     */
    public function loginByFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        $findUserSocial = $this->service->findBySocialProfileEmail('facebook_' . $facebookUser->getId());

        $findUser = $this->userService->findByEmail($facebookUser->getEmail());

        if (!empty($findUserSocial && $findUser)) {

            return $this->loginSocial($findUser);
        } elseif (!empty($findUser) && empty($findUserSocial)) {
            $this->service->create([
                'social_profile_key' => 'facebook_' . $facebookUser->getId(),
                'social_network_uuid' => 'facebook',
                'user_uuid' => $findUser->uuid,
                'social_profile_name' => $facebookUser->getName(),
                'social_profile_avatar' => $facebookUser->getAvatar(),
                'social_profile_email' => $facebookUser->getEmail(),
                'updated_info_at' => Carbon::now(),
            ]);

            return $this->loginSocial($findUser);
        } else {
            $newUser = $this->userService->create([
                'email' => $facebookUser->getEmail(),
                'username' => $facebookUser->getEmail(),
                'password' => Hash::make(Str::random(20)),
            ]);

            $newUser->roles()->attach([config('user.default_role_uuid')]);

            $this->service->create([
                'social_profile_key' => 'facebook_' . $facebookUser->getId(),
                'social_network_uuid' => 'facebook',
                'user_uuid' => $newUser->uuid,
                'social_profile_name' => $facebookUser->getName(),
                'social_profile_avatar' => $facebookUser->getAvatar(),
                'social_profile_email' => $facebookUser->getEmail(),
                'updated_info_at' => Carbon::now(),
            ]);

            return $this->loginSocial($newUser);
        }
    }

    /**
     * @param $userSocial
     * @return Application|Redirector|JsonResponse|RedirectResponse
     */
    protected function loginSocial($userSocial)
    {
        $isUserCanLogin = $this->authenticationService->doesUserCanLogin($userSocial);

        if (is_array($isUserCanLogin)) {

            return $this->sendUnAuthorizedJsonResponse($isUserCanLogin);
        }

        if ($isUserCanLogin == true) {
            $userData = app(UserResource::class, ['resource' => $userSocial])
                ->toResponse(app('Request'))
                ->getData(true);

            $userData['data']['token'] = $this->userAccessTokenService->storeNewForUser($userSocial)->getKey();
            $userData['data']['token_type'] = 'Bearer';

            return redirect(URL::to(config('auth.login_succeed_redirect_url')))
                ->withCookie(
                    \cookie('accessToken', $userData['data']['token'], config('auth.password_timeout'), null, null, false, true)
                )->withCookie(
                    \cookie('logged', true, config('auth.password_timeout'), null, null, false, false)
                );
        }

        return redirect(URL::to(config('auth.login_failed_redirect_url')));
    }
}
