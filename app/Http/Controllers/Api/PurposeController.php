<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PurposeRequest;
use App\Http\Requests\UpdatePurposeRequest;
use App\Http\Resources\PurposeResource;
use App\Http\Resources\PurposeResourceCollection;
use App\Models\Purpose;
use App\Services\LanguageService;
use App\Services\PurposeService;
use Illuminate\Http\JsonResponse;

class PurposeController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param PurposeService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        PurposeService $service,
        LanguageService $languageService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PurposeResourceCollection::class;
        $this->resourceClass = PurposeResource::class;
        $this->storeRequest = PurposeRequest::class;
        $this->editRequest = UpdatePurposeRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->languageService = $languageService;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (!$this->languageService->checkLanguages($request->title)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create($request->except('publish_status'));

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

        if ($request->title && !$this->languageService->checkLanguages($request->title)) {
            return $this->sendValidationFailedJsonResponse();
        }
        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function indexPublic(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => Purpose::PUBLISHED_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
