<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ParagraphTypeRequest;
use App\Http\Requests\UpdateParagraphTypeRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Resources\ParagraphTypeResource;
use App\Http\Resources\ParagraphTypeResourceCollection;
use App\Services\LanguageService;
use App\Services\ParagraphTypeService;

class ParagraphTypeController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param ParagraphTypeService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        ParagraphTypeService $service,
        LanguageService      $languageService
    )
    {
        $this->service = $service;
        $this->languageService = $languageService;
        $this->resourceCollectionClass = ParagraphTypeResourceCollection::class;
        $this->resourceClass = ParagraphTypeResource::class;
        $this->storeRequest = ParagraphTypeRequest::class;
        $this->editRequest = UpdateParagraphTypeRequest::class;
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
             'user_uuid' => auth()->user(),
            'app_id' => auth()->appId(),
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

        //Update sort field by UUID
        $this->service->updateSortFieldByUuid($request->children_uuid);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
