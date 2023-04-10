<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerLevelRequest;
use App\Http\Requests\UpdatePartnerLevelRequest;
use App\Http\Resources\PartnerLevelResource;
use App\Http\Resources\PartnerLevelResourceCollection;
use App\Services\LanguageService;
use App\Services\PartnerLevelService;
use Illuminate\Http\JsonResponse;

class PartnerLevelController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    public function __construct(
        PartnerLevelService $service,
        LanguageService $languageService)
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerLevelResourceCollection::class;
        $this->resourceClass = PartnerLevelResource::class;
        $this->storeRequest = PartnerLevelRequest::class;
        $this->editRequest = UpdatePartnerLevelRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->languageService = $languageService;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (!$this->languageService->checkLanguages($request->title)
            || !$this->languageService->checkLanguages($request->content)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create($request->all());

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        if (($request->title && !$this->languageService->checkLanguages($request->title))
            || ($request->content && !$this->languageService->checkLanguages($request->content))) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}