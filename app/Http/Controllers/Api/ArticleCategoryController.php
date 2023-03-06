<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\ArticleCategoryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateArticleCategoryRequest;
use App\Http\Resources\ArticleCategoryResource;
use App\Http\Resources\ArticleCategoryResourceCollection;
use App\Models\Language;
use App\Services\ArticleCategoryService;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleCategoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param ArticleCategoryService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        ArticleCategoryService $service,
        LanguageService $languageService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = ArticleCategoryResourceCollection::class;
        $this->resourceClass = ArticleCategoryResource::class;
        $this->storeRequest = ArticleCategoryRequest::class;
        $this->editRequest = UpdateArticleCategoryRequest::class;
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

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getKey()
        ]));

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

        $this->service->update($model, $request->except('user_uuid'));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
