<?php

namespace App\Http\Controllers\Api\User;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailVerifyEmailEvent;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IncrementUserProfileRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\LoadAnalyticDataRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserProfileDailyTrackingCollection;
use App\Http\Resources\UserProfileDailyTrackingResource;
use App\Http\Resources\UserProfileMonthlyTrackingCollection;
use App\Http\Resources\UserProfileMonthlyTrackingResource;
use App\Http\Resources\UserResource;
use App\Services\UserProfileDailyTrackingService;
use App\Services\UserProfileMonthlyTrackingService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestIndexTrait;

    /**
     * @var UserProfileDailyTrackingService
     */
    protected $userProfileDailyTrackingService;

    /**
     * @var UserProfileMonthlyTrackingService
     */
    protected $userProfileMonthlyTrackingService;

    /**
     * UserController constructor.
     * @param UserService $service
     * @param UserProfileDailyTrackingService $userProfileDailyTrackingService
     * @param UserProfileMonthlyTrackingService $userProfileMonthlyTrackingService
     */
    public function __construct(
        UserService $service,
        UserProfileDailyTrackingService $userProfileDailyTrackingService,
        UserProfileMonthlyTrackingService $userProfileMonthlyTrackingService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserCollection::class;
        $this->resourceClass = UserResource::class;
        $this->storeRequest = UserRequest::class;
        $this->editRequest = UpdateUserRequest::class;
        $this->userProfileDailyTrackingService = $userProfileDailyTrackingService;
        $this->userProfileMonthlyTrackingService = $userProfileMonthlyTrackingService;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @param $username
     * @return JsonResponse
     */
    public function showByUserName($username)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $this->service->showByUserName($username))
        );
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->all(), [
            'password' => Hash::make($request->get('password')),
        ]));

        $model->roles()->sync(
            array_merge($request->get('roles', []), [config('user.default_role_uuid')])
        );

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, array_merge($request->all(), [
            'password' => Hash::make($request->get('password'))
        ]));

        $model->roles()->sync($request->roles ? $request->roles : $model->roles);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function editMyProfile()
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById(auth()->user()->getkey());

        $this->service->update($model, array_merge($request->all(), [
            'password' => Hash::make($request->get('password'))
        ]));

        $model->roles()->sync($request->roles ? $request->roles : $model->roles);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IncrementUserProfileRequest $request
     * @return JsonResponse
     */
    public function incrementProfileTrackingView(IncrementUserProfileRequest $request)
    {
        $secretKey = md5(substr(config('app.name'), 0, 3) .".". config('secretkey.front_end_app_key') .".". $request->get('user_uuid') .".". $request->get('timestamps'));

        if ($request->get('secret_key') === $secretKey) {
            $dailyTrackingData = $this->service->resourceToData(
                UserProfileDailyTrackingResource::class,
                $this->userProfileDailyTrackingService->incrementTodayVisitorViewByUserProfileKey($request->get('user_uuid'))
            );

            $monthlyTrackingData = $this->service->resourceToData(
                UserProfileMonthlyTrackingResource::class,
                $this->userProfileMonthlyTrackingService->incrementTodayVisitorViewByUserProfileKey($request->get('user_uuid'))
            );

            return $this->sendOkJsonResponse([
                'data' => [
                    'daily' => $dailyTrackingData['data'],
                    'monthly' => $monthlyTrackingData['data'],
                ]
            ]);
        }

        return $this->sendValidationFailedJsonResponse(['secret_key' => __('messages.secret_key_invalid')]);
    }

    /**
     * @return JsonResponse|void
     */
    public function verifyMyEmail()
    {
        $user = $this->service->findOneWhereOrFail([
            'email' => auth()->user()->email
        ]);

        if (empty($user->email_verified_at)) {
            Event::dispatch(new SendEmailVerifyEmailEvent($user));
        } else {
            return $this->sendValidationFailedJsonResponse(['email' => __('messages.email_already_verified')]);
        }
    }

    /**
     * @param $pin
     * @return JsonResponse
     */
    public function checkVerificationCode($pin)
    {
        $model = $this->service->findOneWhereOrFail([
            'email' => auth()->user()->email
        ]);

        if ($pin == $model->email_verification_code) {
            $this->service->update($model, array_merge(json_decode($model->all()), [
                'email_verified_at' => Carbon::now()
            ]));

            return $this->sendJsonResponse(true, __('Success'));
        } else {

            return $this->sendValidationFailedJsonResponse(['pin' => __('messages.pin_invalid')]);
        }
    }

    /**
     * @param LoadAnalyticDataRequest $request
     * @return JsonResponse|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function loadMyAnalyticData(LoadAnalyticDataRequest $request)
    {
        $type = $request->get('type', 'daily');

        if ($type == 'daily') {

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData(
                    UserProfileDailyTrackingCollection::class,
                    $this->userProfileDailyTrackingService->loadMyProfileDailyAnalytic()
                )
            );
        } else if ($type == 'monthly') {

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData(
                    UserProfileMonthlyTrackingCollection::class,
                    $this->userProfileMonthlyTrackingService->loadMyProfileMonthlyAnalytic()
                )
            );
        }
    }
}
