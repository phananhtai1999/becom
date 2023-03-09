<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailRecoveryPasswordEvent;
use App\Http\Requests\RecoveryPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendResetPasswordEmailRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\LoginRequest;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\RoleService;
use App\Services\AuthenticationService;
use App\Services\PasswordResetService;
use App\Services\UserAccessTokenService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class AuthController extends AbstractRestAPIController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var PasswordResetService
     */
    private $passwordResetService;

    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * @var UserAccessTokenService
     */
    private $userAccessTokenService;

    /**
     * @param UserService $userService
     * @param AuthenticationService $authenticationService
     * @param PasswordResetService $passwordResetService
     * @param RoleService $roleService
     * @param UserAccessTokenService $userAccessTokenService
     */
    public function __construct(
        UserService $userService,
        AuthenticationService $authenticationService,
        PasswordResetService $passwordResetService,
        RoleService $roleService,
        UserAccessTokenService $userAccessTokenService
    )
    {
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->passwordResetService = $passwordResetService;
        $this->roleService = $roleService;
        $this->userAccessTokenService = $userAccessTokenService;
    }

    /**
     * @param LoginRequest $loginRequest
     * @return JsonResponse
     */
    public function login(LoginRequest $loginRequest)
    {
        $user = $this->userService->findUserLogin($loginRequest->get('email'));
        $isUserCanLogin = $this->authenticationService->doesUserCanLogin($user);
        if (is_array($isUserCanLogin)) {
            return $this->sendUnAuthorizedJsonResponse($isUserCanLogin);
        }

        if ($isUserCanLogin == true &&
            $this->guard()
                ->attempt($loginRequest->only(['email', 'password']), $loginRequest->filled('remember'))
        ) {
            $userData = app(UserResource::class, ['resource' => $user])
                ->toResponse(app('Request'))
                ->getData(true);

            $userData['data']['token'] = $this->userAccessTokenService->storeNewForUser($user)->getKey();
            $userData['data']['token_type'] = 'Bearer';
            return \response()->json(array_merge([
                'status' => true,
                "code" => 0,
                "locale" => app()->getLocale(),
                'message' => __("messages.login_success")
            ], $userData))
                ->withCookie(
                    \cookie('accessToken', $userData['data']['token'], config('auth.password_timeout'), null, null, true, true)
                )->withCookie(
                    \cookie('logged', true, config('auth.password_timeout'), null, null, true, false)
                );
        }

        return $this->sendUnAuthorizedJsonResponse();
    }

    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return \response()->json([
            'status' => true,
            "code" => 0,
            "locale" => app()->getLocale(),
            'message' => __('messages.logout_success')
        ])
            ->withoutCookie('accessToken')
            ->withoutCookie('logged');
    }

    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->userService->currentUser();
        if ($user) {
            return $this->sendOkJsonResponse(
                app(UserResource::class, ['resource' => $user])
                    ->toResponse(app('Request'))
                    ->getData(true)
            );
        }

        return $this->sendUnAuthorizedJsonResponse();
    }

    /**
     * @param RecoveryPasswordRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function recoveryPassword(RecoveryPasswordRequest $request): JsonResponse
    {
        /** @var PasswordReset $passwordReset */
        $passwordReset = $this->passwordResetService
            ->findOneWhere(['token' => $request->get('token')]);

        if ($passwordReset) {
            /** @var User $user */
            $user = $this->userService
                ->findOneWhere(['email' => $passwordReset->email]);

            if ($user) {
                $passwordReset->delete();

                $user->update([
                    'password' => Hash::make($request->get('password'))
                ]);

                return $this->sendOkJsonResponse(['message' => __('messages.change_password_success')]);
            }
        }

        return $this->sendValidationFailedJsonResponse(['token' => __('messages.token_does_not_exists')]);
    }

    /**
     * @param RegisterRequest $registerRequest
     * @return JsonResponse
     */
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        /** @var User $user */
        $user = $this->userService->create(array_merge($registerRequest->all(), [
            'password' => Hash::make($registerRequest->get('password')),
            'can_add_smtp_account' => "1"
        ]));

        if ($user) {
            $user->roles()->attach([config('user.default_role_uuid')]);

            $userData = app(UserResource::class, ['resource' => $user])
                ->toResponse(app('Request'))
                ->getData(true);

            $userData['data']['token'] = $this->userAccessTokenService->storeNewForUser($user)->getKey();
            $userData['data']['token_type'] = 'Bearer';

            return \response()->json(array_merge([
                'status' => true,
                "code" => 0,
                "locale" => app()->getLocale(),
                'message' => __("messages.register_success")
            ], $userData))
                ->withCookie(
                    \cookie('accessToken', $userData['data']['token'], config('auth.password_timeout'), null, null, false, true)
                )->withCookie(
                    \cookie('logged', true, config('auth.password_timeout'), null, null, false, false)
                );
        }

        return $this->sendInternalServerErrorJsonResponse();
    }

    /**
     * @param SendResetPasswordEmailRequest $request
     * @return JsonResponse
     */
    public function forgetPassword(SendResetPasswordEmailRequest $request): JsonResponse
    {
        $user = $this->userService->findOneWhereOrFail([
            'email' => $request->get('email')
        ]);

        if ($user) {
            Event::dispatch(new SendEmailRecoveryPasswordEvent($user));

            return $this->sendOkJsonResponse(['message' => __('messages.reset_password')]);
        }

        return $this->sendValidationFailedJsonResponse(['email' => __('messages.email_does_not_exists')]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkToken(Request $request)
    {
        /** @var PasswordReset $passwordReset */
        $passwordReset = $this->passwordResetService
            ->findOneWhere(['token' => $request->get('token')]);

        if ($passwordReset) {
            return $this->sendOkJsonResponse(['message' => __('messages.success')]);
        }

        return $this->sendValidationFailedJsonResponse(['token' => __('messages.token_does_not_exists')]);
    }
}
