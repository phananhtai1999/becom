<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
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
use App\Models\Partner;
use App\Services\PartnerLevelService;
use App\Services\PartnerService;
use Illuminate\Http\JsonResponse;

class PartnerController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    protected $partnerLevelService;

    public function __construct(
        PartnerService $service,
        PartnerLevelService $partnerLevelService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerResourceCollection::class;
        $this->resourceClass = PartnerResource::class;
        $this->storeRequest = PartnerRequest::class;
        $this->editRequest = UpdatePartnerRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->partnerLevelService = $partnerLevelService;
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

        $this->service->update($model, [
           'publish_status' => $request->get('publish_status')
        ]);

        return $this->sendOkJsonResponse();
    }
}
