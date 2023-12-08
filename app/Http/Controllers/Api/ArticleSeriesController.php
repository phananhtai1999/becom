<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ArticleSeriesRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateArticleSeriesRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Resources\ArticleSeriesResource;
use App\Http\Resources\ArticleSeriesResourceCollection;
use App\Services\ArticleSeriesService;
use App\Services\LanguageService;

class ArticleSeriesController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param ArticleSeriesService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        ArticleSeriesService $service,
        LanguageService      $languageService
    )
    {
        $this->service = $service;
        $this->languageService = $languageService;
        $this->resourceCollectionClass = ArticleSeriesResourceCollection::class;
        $this->resourceClass = ArticleSeriesResource::class;
        $this->storeRequest = ArticleSeriesRequest::class;
        $this->editRequest = UpdateArticleSeriesRequest::class;
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

        $model = $this->service->create($request->except(['article_uuid']));

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

        $this->service->update($model, $request->except(['article_uuid']));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexMyAssigned(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            [
                'assigned_ids' => auth()->user(),
                'app_id' => auth()->appId()
            ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
