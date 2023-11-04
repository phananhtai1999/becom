<?php

namespace App\Http\Controllers\Api\User;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailVerifyEmailEvent;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AdminUserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Http\Requests\UpdateMyProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResourceCollection;
use App\Http\Resources\UserResource;
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
     * UserController constructor.
     * @param UserService $service
     */
    public function __construct(
        UserService $service
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserResourceCollection::class;
        $this->resourceClass = UserResource::class;
        $this->storeRequest = UserRequest::class;
        $this->editRequest = UpdateUserRequest::class;
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
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->createUserByRequest($request);

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

        $data = $request->all();

        if ($request->can_add_smtp_account == '0') {
            $data = array_merge($data, [
                'can_add_smtp_account' => '0'
            ]);
        } elseif (empty($request->can_add_smtp_account)) {
            $data = array_merge($data, [
                'can_add_smtp_account' => $model->can_add_smtp_account
            ]);
        }

        if ($request->has('password')) {
            $data = array_merge($data, [
                'password' => Hash::make($request->get('password'))
            ]);
        }

        $this->service->update($model, array_merge($data, [
            'credit' => $model->credit
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
    public function editMyProfile(UpdateMyProfileRequest $request)
    {
        $model = $this->service->findOrFailById(auth()->user()->getkey());

        $data = $request->only(['first_name', 'last_name', 'avatar_img', 'cover_img']);
        $this->service->update($model, $data);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
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
     * @param $id
     * @return JsonResponse
     */
    public function ban($id)
    {
        $model = $this->service->findOrFailById($id);
        $timezone = optional($this->service->getConfigByKeyInCache('timezone'))->value;

        $this->service->update($model, ['banned_at' => Carbon::now($timezone)]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function unBan($id)
    {
        $model = $this->service->findOrFailById($id);
        $this->service->update($model, ['banned_at' => null]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param ChartRequest $request
     * @return JsonResponse
     */
    public function userChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');
        $totalBannedAndActive = $this->service->totalBannedAndActive($startDate, $endDate);
        $userChart = $this->service->userChart($groupBy, $startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $userChart,
            'total' => $totalBannedAndActive['0']
        ]);
    }

    /**
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        if (Hash::check($request->get('old_password'), $user->password)) {
            $this->service->update($user, [
                'password' => Hash::make($request->get('new_password'))
            ]);

            return $this->sendOkJsonResponse(["messages" => __('messages.change_password_success')]);
        }
        return $this->sendValidationFailedJsonResponse(["errors" => ["old_password" => __('messages.old_password_invalid')]]);
    }

    public function indexAdmin(IndexRequest $request)
    {
        $models = $this->service->getUsersOfAdmin($request);
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showAdmin($id)
    {
        $model = $this->service->showUserOfAdminById($id);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function banAdmin($id)
    {
        $model = $this->service->showUserOfAdminById($id);
        $timezone = optional($this->service->getConfigByKeyInCache('timezone'))->value;

        $this->service->update($model, ['banned_at' => Carbon::now($timezone)]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function unBanAdmin($id)
    {
        $model = $this->service->showUserOfAdminById($id);
        $this->service->update($model, ['banned_at' => null]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function storeAdmin(AdminUserRequest $request)
    {
        $model = $this->service->createUserByRequest($request);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function editAdmin($id, UpdateAdminUserRequest $request)
    {
        $model = $this->service->showUserOfAdminById($id);
        $data = $request->except('credit');

        if ($request->has('password')) {
            $data = array_merge($data, [
                'password' => Hash::make($request->get('password'))
            ]);
        }

        $this->service->update($model, $data);

        $model->roles()->sync($request->roles ?? $model->roles);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function destroyAdmin($id)
    {
        $this->service->deleteUserOfAdminById($id);

        return $this->sendOkJsonResponse();
    }

    public function getPermissionOfUser($userId)
    {
        return parent::checkTeamOwner($teamId); // TODO: Change the autogenerated stub
    }
}
