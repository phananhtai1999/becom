<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleResourceCollection;
use App\Models\Language;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;

class ArticleController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @param ArticleService $service
     */
    public function __construct(
        ArticleService $service
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = ArticleResourceCollection::class;
        $this->resourceClass = ArticleResource::class;
        $this->storeRequest = ArticleRequest::class;
        $this->editRequest = UpdateArticleRequest::class;
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

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->except('user_uuid'));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
