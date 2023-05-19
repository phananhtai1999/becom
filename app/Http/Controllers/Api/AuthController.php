<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailRecoveryPasswordEvent;
use App\Events\SendNotificationSystemForLoginEvent;
use App\Http\Requests\RecoveryPasswordRequest;
use App\Http\Requests\RefreshOtpRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendResetPasswordEmailRequest;
use App\Http\Requests\VerifyActiveCodeRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\LoginRequest;
use App\Mail\SendActiveCode;
use App\Models\Invite;
use App\Models\PasswordReset;
use App\Models\PlatformPackage;
use App\Models\User;
use App\Services\ConfigService;
use App\Services\InviteService;
use App\Services\OtpService;
use App\Services\PartnerService;
use App\Services\PartnerUserService;
use App\Services\RoleService;
use App\Services\AuthenticationService;
use App\Services\PasswordResetService;
use App\Services\SmtpAccountService;
use App\Services\UserAccessTokenService;
use App\Services\UserService;
use App\Services\UserTeamService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
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

    private $partnerService;

    private $partnerUserService;

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
        OtpService             $otpService,
        ConfigService $configService,
        SmtpAccountService $smtpAccountService,
        InviteService $inviteService,
        UserTeamService $userTeamService,
        PartnerService $partnerService,
        PartnerUserService $partnerUserService
    )
    {
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->passwordResetService = $passwordResetService;
        $this->roleService = $roleService;
        $this->userAccessTokenService = $userAccessTokenService;
        $this->otpService = $otpService;
        $this->configService = $configService;
        $this->smtpAccountService = $smtpAccountService;
        $this->inviteService = $inviteService;
        $this->userTeamService = $userTeamService;
        $this->partnerService = $partnerService;
        $this->partnerUserService = $partnerUserService;
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
            $this->checkInvite($user);
            $otpConfig = $this->configService->findOneWhereOrFail(['key' => 'otp_status']);
            if ($otpConfig->value) {

                return $this->sendOtp('login', $user);
            }

            return $this->generateCookie($user, __("messages.login_success"));
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
            if ($registerRequest->get('invite_uuid')) {
                $model = $this->inviteService->findOrFailById($registerRequest->get('invite_uuid'));
                $this->inviteService->update($model, ['status' => Invite::ACTIVE_STATUS, 'is_logged' => true]);
                $this->userTeamService->create([
                    'team_uuid' => $model->team_uuid,
                    'user_uuid' => $user->uuid,
                ]);
            }
            if (Cookie::has('invitePartner')) {
                $this->partnerUserService->create([
                    'user_uuid' => $user->uuid,
                    'registered_from_partner_code' => Cookie::get('invitePartner')
                ]);
            }
            $user->roles()->attach([config('user.default_role_uuid')]);
            $user->userPlatformPackage()->create(['platform_package_uuid' => PlatformPackage::DEFAULT_PLATFORM_PACKAGE_1]);
            $otpConfig = $this->configService->findOneWhereOrFail(['key' => 'otp_status']);
            if ($otpConfig->value) {

                return $this->sendOtp('register', $user);
            }

            return $this->generateCookie($user, __("messages.register_success"));
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

    /**
     * @param $type
     * @param $user
     * @return JsonResponse
     */
    public function sendOtp($type, $user)
    {
        $activeCode = $this->generateRandomString();
        if ($type == 'register') {
            $otp = $this->otpService->create([
                'active_code' => $activeCode,
                'user_uuid' => $user->uuid,
                'expired_time' => Carbon::now()->addMinutes(config('otp.expired_time')),
                'refresh_time' => Carbon::now()->addSeconds(config('otp.refresh_time')),
            ]);
        } else {
            $this->otpService->firstOrCreate(['user_uuid' => $user->uuid], ['user_uuid' => $user->uuid]);
            $otp = $this->otpService->findOrFailById($user->uuid);
            if (!empty($otp->blocked_time) && $otp->blocked_time > Carbon::now()) {

                return $this->sendValidationFailedJsonResponse(['message' => __('auth.account_blocked')]);
            } elseif (!empty($otp->expired_time) && $otp->expired_time > Carbon::now()) {

                return $this->sendOkJsonResponse(['data' => [
                    'is_verified' => false,
                    'roles' => $user->roles,
                    'email' => $user->email
                ]]);
            }
            $this->otpService->update($otp,[
                'active_code' => $activeCode,
                'expired_time' => Carbon::now()->addMinutes(config('otp.expired_time')),
                'refresh_time' => Carbon::now()->addSeconds(config('otp.refresh_time')),
            ]);
        }
        $this->smtpAccountService->sendEmailNotificationSystem($user, new SendActiveCode($user, $otp));

        return $this->sendOkJsonResponse(['data' => [
            'is_verified' => false,
            'roles' => $user->roles,
            'email' => $user->email
        ]]);
    }

    /**
     * @param RefreshOtpRequest $request
     * @return JsonResponse
     */
    public function refreshOtp(RefreshOtpRequest $request)
    {
        $user = $this->userService->findByEmail($request->get('email'));
        $otp = $this->otpService->findOrFailById($user->uuid);
        if ($otp->refresh_time > Carbon::now()) {

            return $this->sendValidationFailedJsonResponse(['message'=> 'Please wait ' . config('otp.refresh_time') . ' second to refresh!']);
        } elseif (empty($otp->active_code)) {

            return $this->sendValidationFailedJsonResponse(['message' => __('auth.active_code_null')]);
        }
        $activeCode = $this->generateRandomString();
        $refreshCount = $otp->refresh_count + 1;
        if ($refreshCount > config('otp.refresh_count')) {

            return $this->sendValidationFailedJsonResponse(['message' => __('auth.refresh_exceeded')]);
        }
        $this->otpService->update($otp, [
            'active_code' => $activeCode,
            'expired_time' => Carbon::now()->addMinutes(config('otp.expired_time')),
            'refresh_time' => Carbon::now()->addSeconds(config('otp.refresh_time')),
            'refresh_count' => $refreshCount,
        ]);
        $this->smtpAccountService->sendEmailNotificationSystem($user, new SendActiveCode($user, $otp));

        return $this->sendOkJsonResponse(['message' => __('auth.refresh_code')]);
    }

    /**
     * @param VerifyActiveCodeRequest $request
     * @return JsonResponse|void
     */
    public function verifyActiveCode(VerifyActiveCodeRequest $request) {
        $user = $this->userService->findByEmail($request->get('email'));
        $otp = $this->otpService->findOrFailById($user->uuid);
        if ($otp->expired_time < Carbon::now()) {

            return $this->sendValidationFailedJsonResponse(['message' => __('auth.expired_code')]);
        } elseif ($otp->blocked_time > Carbon::now()) {

            return $this->sendValidationFailedJsonResponse(['message' => __('auth.account_blocked')]);
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

                return $this->sendValidationFailedJsonResponse(['message' => __('auth.wrong_code')]);
            }
        }

        if ($otp->active_code == $request->get('active_code')) {
            $this->otpService->update($otp, [
                'wrong_count' => 0,
                'refresh_count' => 0,
                'active_code' => null,
                'refresh_time' => null,
                'expired_time' => null,
            ]);

            return $this->generateCookie($user, __("messages.login_success"));
        }
    }

    /**
     * @param mixed $user
     * @param $message
     * @return JsonResponse
     */
    public function generateCookie(mixed $user, $message): JsonResponse
    {
        $userData = app(UserResource::class, ['resource' => $user])
            ->toResponse(app('Request'))
            ->getData(true);

        $userData['data']['token'] = $this->userAccessTokenService->storeNewForUser($user)->getKey();
        $userData['data']['token_type'] = 'Bearer';

        //Kiểm tra country và gửi email khi khác country
        SendNotificationSystemForLoginEvent::dispatch($user, \request()->ip(), \request()->userAgent());
        Cache::forget('platform_permission_' . $user->uuid);
        Cache::forget('add_on_permission_' . $user->uuid);
        return \response()->json(array_merge([
            'status' => true,
            "code" => 0,
            "locale" => app()->getLocale(),
            'message' => $message
        ], $userData))
            ->withCookie(
                \cookie('accessToken', $userData['data']['token'], config('auth.password_timeout'), null, null, false, true)
            )->withCookie(
                \cookie('logged', true, config('auth.password_timeout'), null, null, false, false)
            )->withoutCookie('invitePartner');
    }

    public function checkInvite($user)
    {
        $invite = $this->inviteService->findOneWhere(['email' => $user->email]);
        if ($invite && $invite->is_logged) {
            $this->inviteService->update($invite, ['status' => Invite::ACTIVE_STATUS, 'is_logged' => true]);
        }
    }
}

