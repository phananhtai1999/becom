<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\DomainRequest;
use App\Http\Requests\DomainVerificationRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Http\Requests\UpdateMyDomainRequest;
use App\Http\Resources\DomainResource;
use App\Http\Resources\DomainResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Resources\DomainVerificationResource;
use App\Services\BusinessManagementService;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\DomainService;
use App\Services\DomainVerificationService;
use App\Services\MyDomainService;

class DomainController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var DomainVerificationService
     */
    protected $domainVerificationService;

    /**
     * @var MyDomainService
     */
    protected $myService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var BusinessManagementService
     */
    protected $businessManagementService;

    /**
     * @param DomainService $service
     * @param MyDomainService $myService
     * @param DomainVerificationService $domainVerificationService
     * @param ConfigService $configService
     * @param BusinessManagementService $businessManagementService
     */
    public function __construct(
        DomainService             $service,
        MyDomainService           $myService,
        DomainVerificationService $domainVerificationService,
        ConfigService             $configService,
        BusinessManagementService $businessManagementService
    )
    {
        $this->service = $service;
        $this->configService = $configService;
        $this->businessManagementService = $businessManagementService;
        $this->myService = $myService;
        $this->domainVerificationService = $domainVerificationService;
        $this->resourceCollectionClass = DomainResourceCollection::class;
        $this->resourceClass = DomainResource::class;
        $this->storeRequest = DomainRequest::class;
        $this->editRequest = UpdateDomainRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->except(['active_mailbox_status']), [
            'owner_uuid' => $request->get('owner_uuid') ?? auth()->userId(),
            'app_id' => auth()->appId(),
            'active_mailbox' => false,
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, array_merge($request->except(['active_mailbox', 'active_mailbox_status']), [
            'owner_uuid' => $request->get('owner_uuid') ?? $model->owner_uuid,
            'business_uuid' => $request->get('business_uuid') ?? null
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param MyDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyDomain(MyDomainRequest $request)
    {
        $model = $this->service->create(array_merge($request->except(['active_mailbox_status']), [
            'owner_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'active_mailbox' => false,
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyDomain($id)
    {
        $model = $this->myService->showMyDomain($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyDomainRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyDomain(UpdateMyDomainRequest $request, $id)
    {
        $model = $this->myService->showMyDomain($id);

        $this->service->update($model, array_merge($request->except(['active_mailbox', 'active_mailbox_status']), [
            'owner_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyDomain($id)
    {
        $this->myService->deleteMyDomain($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param DomainVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyByDnsRecord(DomainVerificationRequest $request)
    {
        try {
            $domain = $this->service->findOneWhereOrFail([
                'name' => $request->get('domain')
            ]);

            $domainVerify = $this->domainVerificationService->verifyByDnsRecord($domain->getKey());
            //Update domain verified
            if ($domainVerify->verified_at) {
                $this->service->updateDomainVerified($domainVerify);
            }

            return $this->sendOkJsonResponse(
                $this->service->resourceToData(DomainVerificationResource::class, $domainVerify)
            );
        } catch (\Exception $exception) {
            return $this->sendValidationFailedJsonResponse();
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkActiveMailBox($id)
    {
        //Check Domain Valid Or not
        $domainUuidValidOrNot = $this->service->checkDomainValidOrNot($id);
        if ($domainUuidValidOrNot) {
            $configMailboxMx = $this->configService->findConfigByKey('mailbox_mx_domain');
            $configMailboxDmarc = $this->configService->findConfigByKey('mailbox_dmarc_domain');
            $configMailboxDkim = $this->configService->findConfigByKey('mailbox_dkim_domain');
            //update active mailbox status and active mailbox
            $this->service->updateActiveMailboxStatusDomain($id, $configMailboxMx, $configMailboxDmarc, $configMailboxDkim);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function domainVerifiedAndActiveMailbox(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['verified_at', '<>', null],
            ['active_mailbox', true]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myDomainVerifiedAndActiveMailbox(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['owner_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['verified_at', '<>', null],
            ['active_mailbox', true]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
