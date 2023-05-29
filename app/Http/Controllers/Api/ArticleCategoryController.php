<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\Article\ArticleCategoryRequest;
use App\Http\Requests\Article\ChangeStatusArticleCategoryRequest;
use App\Http\Requests\Article\DestroyArticleCategoryRequest;
use App\Http\Requests\Article\UpdateArticleCategoryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\ArticleCategoryResource;
use App\Http\Resources\ArticleCategoryResourceCollection;
use App\Models\ArticleCategory;
use App\Services\ArticleCategoryService;
use App\Services\ArticleService;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;

class ArticleCategoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    protected $articleService;

    /**
     * @param ArticleCategoryService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        ArticleCategoryService $service,
        LanguageService $languageService,
        ArticleService  $articleService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = ArticleCategoryResourceCollection::class;
        $this->resourceClass = ArticleCategoryResource::class;
        $this->storeRequest = ArticleCategoryRequest::class;
        $this->editRequest = UpdateArticleCategoryRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->languageService = $languageService;
        $this->articleService = $articleService;
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

        $this->service->update($model, $request->except(['user_uuid', 'publish_status']));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function indexPublic(IndexRequest $request)
    {
        $models = $this->service->getArticleCategoriesPublicWithPagination(
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
     * @param $id
     * @return JsonResponse
     */
    public function showPublic($id)
    {
        $model = $this->service->showArticleCategoryPublic($id);
        if ($model) {
            return $this->sendOkJsonResponse(
                $this->service->resourceToData($this->resourceClass, $model)
            );
        }
        return $this->sendValidationFailedJsonResponse();

    }

    public function changeStatus($id, ChangeStatusArticleCategoryRequest $request)
    {
        $articleCategory = $this->service->findOrFailById($id);
        $status = $request->get('publish_status');

        if ($status == ArticleCategory::PENDING_PUBLISH_STATUS) {
            $goCatUuid = $request->get('article_category_uuid');

            $catsChildAndSelf = $articleCategory->getDescendantsAndSelf()->pluck('uuid');
            $articles = $this->articleService->findAllWhereIn('article_category_uuid', $catsChildAndSelf, ['uuid', 'article_category_uuid']);
            if (($articles->count() > 0 && !$goCatUuid) || (in_array($goCatUuid, $catsChildAndSelf->toArray()))) {
                return $this->sendValidationFailedJsonResponse(["errors" => ["article_category_uuid" => "The selected article category uuid is invalid"]]);
            }

            $this->articleService->moveArticlesCategoryOfArticles($articles, $goCatUuid);
        }

        $this->service->update($articleCategory, [
            'publish_status' => $status
        ]);

        return $this->sendOkJsonResponse();
    }

    public function deleteCategory($id, DestroyArticleCategoryRequest $request)
    {
        $articleCategory = $this->service->findOrFailById($id);
        $catsChildAndSelf = $articleCategory->getDescendantsAndSelf()->pluck('uuid');

        $goCatUuid = $request->get('article_category_uuid');
        $articles = $this->articleService->findAllWhereIn('article_category_uuid', $catsChildAndSelf, ['uuid', 'article_category_uuid']);
        if (($articles->count() > 0 && !$goCatUuid) || (in_array($goCatUuid, $catsChildAndSelf->toArray()))) {
            return $this->sendValidationFailedJsonResponse(["errors" => ["article_category_uuid" => "The selected article category uuid is invalid"]]);
        }
        $this->articleService->moveArticlesCategoryOfArticles($articles, $goCatUuid);
        $this->service->destroy($id);

        return $this->sendOkJsonResponse();
    }
}
