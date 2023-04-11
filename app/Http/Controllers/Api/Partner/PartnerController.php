<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendAccountForNewPartnerEvent;
use App\Events\SendEmailRecoveryPasswordEvent;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\ChangeStatusPartnerRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerRequest;
use App\Http\Requests\RegisterPartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\PartnerResourceCollection;
use App\Mail\SendAccountForNewPartner;
use App\Models\Partner;
use App\Models\User;
use App\Services\PartnerLevelService;
use App\Services\PartnerService;
use App\Services\PartnerUserService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    protected $partnerLevelService;

    protected $userService;

    protected $smtpAccountService;

    protected $partnerUserService;

    public function __construct(
        PartnerService $service,
        PartnerLevelService $partnerLevelService,
        UserService $userService,
        SmtpAccountService $smtpAccountService,
        PartnerUserService $partnerUserService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerResourceCollection::class;
        $this->resourceClass = PartnerResource::class;
        $this->storeRequest = PartnerRequest::class;
        $this->editRequest = UpdatePartnerRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->partnerLevelService = $partnerLevelService;
        $this->userService = $userService;
        $this->smtpAccountService = $smtpAccountService;
        $this->partnerUserService = $partnerUserService;
    }

    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->except('code'), [
            'publish_status' => 'active',
            'partner_level_uuid' => optional($this->partnerLevelService->getDefaultPartnerLevel())->uuid
        ]));

        return $this->sendOkJsonResponse($this->service->resourceToData($this->resourceClass, $model));
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->except(['code', 'publish_status']));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function registerPartner(RegisterPartnerRequest $request)
    {
        $model = $this->service->create(array_merge($request->except('code'), [
            'publish_status' => 'pending',
            'partner_level_uuid' => optional($this->partnerLevelService->getDefaultPartnerLevel())->uuid
        ]));

        return $this->sendOkJsonResponse($this->service->resourceToData($this->resourceClass, $model));
    }

    public function changeStatusPartner(ChangeStatusPartnerRequest $request)
    {
        $model = $this->service->findOrFailById($request->get('partner_uuid'));
        $code = $model->code;
        $userUuid = $model->user_uuid;
        if (!$model->code && $request->get('publish_status') === 'active') {
            //Random code partner
            $minCode = $this->userService->getMinCodeByNumberOfUser();
            do {
                $code = $this->generateRandomString($minCode);
                $modelCode = $this->service->findOneWhere(['code' => $code]);
            } while ($modelCode !== null);

            //Tạo User khi partner chưa có tài khoản hệ thống
            if (!$model->user_uuid) {
                $password = $this->generateRandomString(6);
                $newUser = $this->userService->create([
                    'email' => $model->partner_email,
                    'username' => $model->partner_email,
                    'can_add_smtp_account' => 0,
                    'password' => Hash::make($password)
                ]);
                $newUser->roles()->attach($request->get('partner_role'));
                $userUuid = $newUser->uuid;
                Event::dispatch(new SendAccountForNewPartnerEvent($newUser));
            }
            //Kiểm tra partner_user nếu có thì update, k có thì create
            $partnerUser = $this->partnerUserService->findOneWhere(['user_uuid' => $userUuid]);
            if ($partnerUser) {
                $this->partnerUserService->update($partnerUser, [
                   'partner_code' => $code
                ]);
            }else {
                $this->partnerUserService->create([
                    'user_uuid' => $userUuid,
                    'partner_code' => $code
                ]);
            }
        }

        $this->service->update($model, [
            'publish_status' => $request->get('publish_status'),
            'code' => $code,
            'user_uuid' => $userUuid
        ]);

        return $this->sendOkJsonResponse();
    }
}
