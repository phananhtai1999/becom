<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\Article\ChangeStatusArticleRequest;
use App\Http\Requests\Article\UnpublishedArticleRequest;
use App\Http\Requests\Article\UpdateUnpublishedArticleRequest;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleResourceCollection;
use App\Models\Article;
use App\Services\ArticleService;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;

class ArticleController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param ArticleService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        ArticleService $service,
        LanguageService $languageService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = ArticleResourceCollection::class;
        $this->resourceClass = ArticleResource::class;
        $this->storeRequest = ArticleRequest::class;
        $this->editRequest = UpdateArticleRequest::class;
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

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getKey(),
            'content_for_user' => $request->content_for_user ?: Article::PUBLIC_CONTENT_FOR_USER
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
        if (($request->title && !$this->languageService->checkLanguages($request->title))
            || ($request->content && !$this->languageService->checkLanguages($request->content))) {

            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->findOrFailById($id);
        //Check current user role
        $role = auth()->user()->roles->whereIn('slug', ["admin"])->count() ? $request->except('user_uuid') : $request->except(['user_uuid', 'publish_status']);
        $this->service->update($model, $role);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function indexPublic(IndexRequest $request)
    {
        $models = $this->service->getArticlePublicWithPagination(
            $request->get('per_page', '15'),
            $request->get('page', '1'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
            $request->get('search'),
            $request->get('search_by'),
        );

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function indexContent(IndexRequest $request)
    {
        $models = $this->service->getArticleContentPublicWithPagination(
            $request->get('per_page', '15'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
            $request->get('page', '1'),
            $request->get('search'),
            $request->get('search_by'),
        );
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function indexManager(IndexRequest $request)
    {
        $models = $this->service->getArticleManagerWithPagination(
            $request->get('per_page', '15'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
            $request->get('page', '1'),
            $request->get('search'),
            $request->get('search_by'),
        );
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showPublic($id)
    {
        $model = $this->service->showArticlePublic($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function indexMy(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['user_uuid' => auth()->user()->getKey()]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function indexUnpublishedArticle(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => Article::PENDING_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showUnpublishedArticle($id)
    {
        $model = $this->service->showArticleForEditorById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UnpublishedArticleRequest $request
     * @return JsonResponse
     */
    public function storeUnpublishedArticle(UnpublishedArticleRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => Article::PENDING_PUBLISH_STATUS,
            'user_uuid' => auth()->user()->getKey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateUnpublishedArticleRequest $request
     * @return JsonResponse
     */
    public function editUnpublishedArticle(UpdateUnpublishedArticleRequest $request, $id)
    {
        $model = $this->service->showArticleForEditorById($id);
        $this->service->update($model, array_merge($request->except(['user_uuid']), [
            'publish_status' => Article::PENDING_PUBLISH_STATUS,
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param ChangeStatusArticleRequest $request
     * @return JsonResponse
     */
    public function changeStatusArticle(ChangeStatusArticleRequest $request)
    {
        $articleUuids = $request->articles;
        foreach ($articleUuids as $articleUuid)
        {
            $model = $this->service->findOneById($articleUuid);
            $this->service->update($model, ['publish_status' => $request->get('publish_status')]);
        }

        return $this->sendOkJsonResponse();
    }
}
