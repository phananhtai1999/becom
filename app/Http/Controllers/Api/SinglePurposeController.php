<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\SinglePurposeRequest;
use App\Http\Requests\UpdateSinglePurposeRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Resources\SinglePurposeResource;
use App\Http\Resources\SinglePurposeResourceCollection;
use App\Services\LanguageService;
use App\Services\SinglePurposeService;

class SinglePurposeController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param SinglePurposeService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        SinglePurposeService $service,
        LanguageService      $languageService
    )
    {
        $this->service = $service;
        $this->languageService = $languageService;
        $this->resourceCollectionClass = SinglePurposeResourceCollection::class;
        $this->resourceClass = SinglePurposeResource::class;
        $this->storeRequest = SinglePurposeRequest::class;
        $this->editRequest = UpdateSinglePurposeRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (!$this->languageService->checkLanguages($request->title)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId()
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        if ($request->title && !$this->languageService->checkLanguages($request->title)) {
            return $this->sendValidationFailedJsonResponse();
        }
        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->except(['user_uuid']));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
