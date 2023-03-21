<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailRecoveryPasswordEvent;
use App\Http\Requests\RecoveryPasswordRequest;
use App\Http\Requests\RefreshOtpRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendResetPasswordEmailRequest;
use App\Http\Requests\VerifyActiveCodeRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\LoginRequest;
use App\Models\PasswordReset;
use App\Models\PlatformPackage;
use App\Models\User;
use App\Services\OtpService;
use App\Services\RoleService;
use App\Services\AuthenticationService;
use App\Services\PasswordResetService;
use App\Services\UserAccessTokenService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        UserService            $userService,
        AuthenticationService  $authenticationService,
        PasswordResetService   $passwordResetService,
        RoleService            $roleService,
        UserAccessTokenService $userAccessTokenService,
        OtpService             $otpService
    )
    {
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->passwordResetService = $passwordResetService;
        $this->roleService = $roleService;
        $this->userAccessTokenService = $userAccessTokenService;
        $this->otpService = $otpService;
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

            return $this->sendOtp('login', $user);
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
            'can_add_smtp_account' => "0"
        ]));

        if ($user) {
            $user->roles()->attach([config('user.default_role_uuid')]);
            $user->userPlatformPackage()->create(['platform_package_uuid' => PlatformPackage::DEFAULT_PLATFORM_PACKAGE_1]);

            return $this->sendOtp('register', $user);
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

    public function sendOtp($type, $user)
    {
//        $activeCode = $this->generateRandomString();
        $activeCode = 1234;
        if ($type == 'register') {
            $this->otpService->create([
                'active_code' => $activeCode,
                'user_uuid' => $user->uuid,
                'expired_time' => Carbon::now()->addMinutes(config('otp.expired_time')),
                'refresh_time' => Carbon::now()->addSeconds(config('otp.refresh_time')),
            ]);
        } else {
            $otp = $this->otpService->firstOrCreate(['user_uuid' => $user->uuid], ['user_uuid' => $user->uuid]);
            if (!empty($otp->blocked_time) && $otp->blocked_time > Carbon::now()) {

                return $this->sendValidationFailedJsonResponse(['message' => 'Your account is blocked. Contact admin to take a help!']);
            } elseif (!empty($otp->expired_time) && $otp->expired_time > Carbon::now()) {

                return $this->sendOkJsonResponse(['data' => [
                    'is_verified' => false,
                    'email' => $user->email
                ]]);
            }
            $this->otpService->update($otp,[
                'active_code' => $activeCode,
                'expired_time' => Carbon::now()->addMinutes(config('otp.expired_time')),
                'refresh_time' => Carbon::now()->addSeconds(config('otp.refresh_time')),
            ]);
        }
        //send otp to email here
        return $this->sendOkJsonResponse(['data' => [
            'is_verified' => false,
            'email' => $user->email
        ]]);
    }

    public function refreshOtp(RefreshOtpRequest $request)
    {
        $user = $this->userService->findByEmail($request->get('email'));
        $otp = $this->otpService->findOrFailById($user->uuid);
        if ($otp->refresh_time > Carbon::now()) {

            return $this->sendValidationFailedJsonResponse(['message'=> 'Please wait ' . config('otp.refresh_time') . ' second to refresh!']);
        }
//        $activeCode = $this->generateRandomString();
        $activeCode = 5678;
        $refreshCount = $otp->refresh_count + 1;
        if ($refreshCount > config('otp.refresh_count')) {

            return $this->sendValidationFailedJsonResponse(['message' => 'The number of refreshes allowed has been exceeded']);
        }
        $this->otpService->update($otp, [
            'active_code' => $activeCode,
            'expired_time' => Carbon::now()->addMinutes(config('otp.expired_time')),
            'refresh_time' => Carbon::now()->addSeconds(config('otp.refresh_time')),
            'refresh_count' => $refreshCount,
        ]);

        return $this->sendOkJsonResponse(['message' => 'Refresh otp successfully']);
    }

    public function verifyActiveCode(VerifyActiveCodeRequest $request) {
        $user = $this->userService->findByEmail($request->get('email'));
        $otp = $this->otpService->findOrFailById($user->uuid);
        if ($otp->expired_time < Carbon::now()) {

            return $this->sendValidationFailedJsonResponse(['message' => 'Your code was expired. Please refresh new code']);
        } elseif ($otp->blocked_time > Carbon::now()) {

            return $this->sendValidationFailedJsonResponse(['message' => 'Your account is blocked. Contact admin to take a help!']);
        } elseif ($otp->active_code != $request->get('active_code')) {
            $wrongCount = $otp->wrong_count + 1;
            if ($wrongCount == config('otp.wrong_count')){
                $this->otpService->update($otp, [
                    'wrong_count' => 0,
                    'refresh_count' => 0,
                    'blocked_time' => Carbon::now()->addMinutes(config('otp.blocked_time')),
                ]);

                return $this->sendValidationFailedJsonResponse(['message' => 'Your code is wrong ' .config('otp.wrong_count') .' times. Please check it again']);
            } else {
                $this->otpService->update($otp, ['wrong_count' => $wrongCount]);

                return $this->sendValidationFailedJsonResponse(['message' => 'Your code is wrong. Please check it again']);
            }
        }

        if ($otp->active_code == $request->get('active_code')) {
            $this->otpService->update($otp, [
                'wrong_count' => 0,
                'refresh_count' => 0,
            ]);
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
    }
}
