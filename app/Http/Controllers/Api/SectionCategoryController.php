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
use App\Http\Resources\SectionCategoryResource;
use App\Http\Resources\SectionCategoryResourceCollection;
use App\Models\Language;
use App\Services\SectionCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionCategoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    public function __construct(SectionCategoryService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = SectionCategoryResourceCollection::class;
        $this->resourceClass = SectionCategoryResource::class;
        $this->storeRequest = WebsitePageCategoryRequest::class;
        $this->editRequest = UpdateWebsitePageCategoryRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        foreach ($request->title as $lang => $value) {
            if (!in_array($lang, Language::LANGUAGES_SUPPORT)) {
                return $this->sendValidationFailedJsonResponse();
            }
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

        foreach ($request->title as $lang => $value) {
            if (!in_array($lang, Language::LANGUAGES_SUPPORT)) {
                return $this->sendValidationFailedJsonResponse();
            }
        }

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
