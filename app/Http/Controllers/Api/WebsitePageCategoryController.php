<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateWebsitePageCategoryRequest;
use App\Http\Requests\WebsitePageCategoryRequest;
use App\Http\Resources\WebsitePageCategoryResource;
use App\Http\Resources\WebsitePageCategoryResourceCollection;
use App\Models\Language;
use App\Models\WebsitePageCategory;
use Techup\ApiConfig\Services\LanguageService;
use App\Services\WebsitePageCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsitePageCategoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestEditTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    public function __construct(
        WebsitePageCategoryService $service,
        LanguageService $languageService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = WebsitePageCategoryResourceCollection::class;
        $this->resourceClass = WebsitePageCategoryResource::class;
        $this->storeRequest = WebsitePageCategoryRequest::class;
        $this->editRequest = UpdateWebsitePageCategoryRequest::class;
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

        if ($request->title && !$this->languageService->checkLanguages($request->title)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
