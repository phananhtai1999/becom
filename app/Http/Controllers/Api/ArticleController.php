<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\Article\ChangeStatusArticleRequest;
use App\Http\Requests\Article\UnpublishedArticleRequest;
use App\Http\Requests\Article\UpdateUnpublishedArticleRequest;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\ChangeStatusMyArticleRequest;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleResourceCollection;
use App\Models\Article;
use App\Models\Role;
use App\Models\UserTeam;
use App\Services\ArticleSeriesService;
use App\Services\ArticleService;
use App\Services\ConfigService;
use App\Services\LanguageService;
use App\Services\TeamService;
use App\Services\UserTeamService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ArticleController extends AbstractRestAPIController
{
    use RestShowTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @var ArticleSeriesService
     */
    protected $articleSeriesService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @param ArticleService $service
     * @param LanguageService $languageService
     * @param ArticleSeriesService $articleSeriesService
     * @param ConfigService $configService
     */
    public function __construct(
        ArticleService       $service,
        LanguageService      $languageService,
        ArticleSeriesService $articleSeriesService,
        ConfigService        $configService,
        UserTeamService $userTeamService
    )
    {
        $this->service = $service;
        $this->configService = $configService;
        $this->articleSeriesService = $articleSeriesService;
        $this->userTeamService = $userTeamService;
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
    public function storeMy(MyArticleRequest $request)
    {
        if (!$this->languageService->checkLanguages($request->title)
            || !$this->languageService->checkLanguages($request->get('content'))
            || ($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        //Map type_label to content
        $content = $this->service->mapTypeLabelToContent($request->get('content'), $request->content_type);
        $userTeam = $this->userTeamService->getUserTeamByUserAndAppId(auth()->userId(), auth()->appId());

        if($userTeam && !$userTeam['is_blocked'] && auth()->userId() != $userTeam->team->owner_uuid) {
            $user_uuid = $userTeam->team->owner_uuid;
            $model = $this->service->create(array_merge($request->except(['reject_reason']), [
                'user_uuid' => $user_uuid,
                'app_id' => auth()->appId(),
                'content_for_user' => $request->content_for_user ?: Article::PUBLIC_CONTENT_FOR_USER,
                'content' => $content,
                'publish_status' => Article::PENDING_PUBLISH_STATUS,
                'description' => $request->keyword ? array_merge($request->keyword, $request->description ?? $request->keyword) : $request->description
            ]));
        } else {
            $user_uuid = auth()->userId();
            $model = $this->service->create(array_merge($request->except(['reject_reason']), [
                'user_uuid' => $user_uuid,
                'app_id' => auth()->appId(),
                'content_for_user' => $request->content_for_user ?: Article::PUBLIC_CONTENT_FOR_USER,
                'content' => $content,
                'description' => $request->keyword ? array_merge($request->keyword, $request->description ?? $request->keyword) : $request->description
            ]));
        }

        // Update Article Series By Article Uuid
        $this->articleSeriesService->updateArticleSeriesByArticleUuid($request->article_series_uuid, $model->uuid);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function showMy($id)
    {
        $user_uuid = $this->getUserUuid();
        $model = $this->service->findOneWhereOrFail([
            ['user_uuid', $user_uuid],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function editMy($id)
    {
        $request = app($this->editRequest);
        if (($request->title && !$this->languageService->checkLanguages($request->title))
            || ($request->content && !$this->languageService->checkLanguages($request->content))
            || ($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }
        $user_uuid = $this->getUserUuid();
        $model = $this->service->findOneWhereOrFail([
            ['user_uuid', $user_uuid],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
        if ($model->content_type != $request->content_type) {
            return $this->sendValidationFailedJsonResponse();
        }
        //Check content exist or not
        $checkContent = $request->content ? array_merge($model->getTranslations('content'), $request->content) : $model->getTranslations('content');
        $content = $this->service->mapTypeLabelToContent($checkContent, $model->content_type);
        //Generate description by keyword and value lang != null
        $description = array_merge(\request('keyword', []), !empty($model->descriptions) ? $model->descriptions : [], array_filter(\request('description', []), function ($value) {
            return $value !== null;
        }));

        $this->service->update($model, array_merge($request->all(), [
            'content' => $content,
            'user_uuid' => $user_uuid,
            'app_id' => auth()->appId(),
            'description' => $description
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (!$this->languageService->checkLanguages($request->title)
            || !$this->languageService->checkLanguages($request->content)
            || ($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        //Map type_label to content
        $content = $this->service->mapTypeLabelToContent($request->content, $request->content_type);

        $model = $this->service->create(array_merge($request->except(['reject_reason']), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'content_for_user' => $request->content_for_user ?: Article::PUBLIC_CONTENT_FOR_USER,
            'content' => $content,
            'description' => $request->keyword ? array_merge($request->keyword, $request->description ?? $request->keyword) : $request->description
        ]));

        // Update Article Series By Article Uuid
        $this->articleSeriesService->updateArticleSeriesByArticleUuid($request->article_series_uuid, $model->uuid);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);
        if (($request->title && !$this->languageService->checkLanguages($request->title))
            || ($request->content && !$this->languageService->checkLanguages($request->content))
            || ($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->findOrFailById($id);

        //Not allow change content_type
        if ($model->content_type != $request->content_type) {
            return $this->sendValidationFailedJsonResponse();
        }
        //Check current user role
        $role = auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN]) ? $request->except('user_uuid') : $request->except(['user_uuid', 'publish_status']);
        //Map type_label to content
        //Check content exist or not
        $checkContent = $request->content ? array_merge($model->getTranslations('content'), $request->content) : $model->getTranslations('content');
        $content = $this->service->mapTypeLabelToContent($checkContent, $model->content_type);
        //Generate description by keyword and value lang != null
        $description = array_merge(\request('keyword', []), !empty($model->descriptions) ? $model->descriptions : [], array_filter(\request('description', []), function ($value) {
            return $value !== null;
        }));

        $this->service->update($model, array_merge($role, [
            'content' => $content,
            'description' => $description
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->service->destroy($id);

        // Update Article Series By Article Uuid
        $this->articleSeriesService->updateArticleSeriesWhenDeleteArticle($id);

        return $this->sendOkJsonResponse();
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
        $role =  auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN, Role::ROLE_USER]);
        $config = $this->configService->findConfigByKey('time_allowed_view_articles_of_editor');
        //Role editor limit by config days
        if (!$role && $config) {
            $models = $this->service->getCollectionWithPaginationByCondition($request, [
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()],
                ['updated_at', '>=', Carbon::now()->subDays($config->value)]
            ]);
        } else {
            $user_uuid = $this->getUserUuid();
            $models = $this->service->getCollectionWithPaginationByCondition($request,
                ['user_uuid' => $user_uuid, 'app_id' => auth()->appId()]);
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function indexUnpublishedArticle(IndexRequest $request)
    {
        $role =  auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN]);
        $config = $this->configService->findConfigByKey('time_allowed_view_articles_of_editor');
        //Role editor limit by config days
        if (!$role && $config) {
            $models = $this->service->getCollectionWithPaginationByCondition($request, [
                ['publish_status', Article::PENDING_PUBLISH_STATUS],
                ['updated_at', '>=', Carbon::now()->subDays($config->value)]
            ]);
        } else {
            $models = $this->service->getCollectionWithPaginationByCondition($request,
                ['publish_status' => Article::PENDING_PUBLISH_STATUS]);
        }

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
        if (!$this->languageService->checkLanguages($request->title)
            || !$this->languageService->checkLanguages($request->get('content'))
            || ($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }
        //Map type_label to content
        $content = $this->service->mapTypeLabelToContent($request->get('content'), $request->content_type);

        $model = $this->service->create(array_merge($request->except(['reject_reason']), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'content' => $content,
            'description' => $request->keyword ? array_merge($request->keyword, $request->description ?? $request->keyword) : $request->description
        ]));

        // Update Article Series By Article Uuid
        $this->articleSeriesService->updateArticleSeriesByArticleUuid($request->article_series_uuid, $model->uuid);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateUnpublishedArticleRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editUnpublishedArticle(UpdateUnpublishedArticleRequest $request, $id)
    {
        $model = $this->service->showArticleForEditorById($id);
        if (($request->title && !$this->languageService->checkLanguages($request->title))
            || ($request->get('content') && !$this->languageService->checkLanguages($request->get('content')))
            || ($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        //Not allow change content_type
        if ($model->content_type != $request->content_type) {
            return $this->sendValidationFailedJsonResponse();
        }

        //Map type_label to content
        //Check content exist or not
        $checkContent = $request->get('content') ? array_merge($model->getTranslations('content'), $request->get('content')) : $model->getTranslations('content');
        $content = $this->service->mapTypeLabelToContent($checkContent, $model->content_type);
        //Generate description by keyword and value lang != null
        $description = array_merge(\request('keyword', []), !empty($model->descriptions) ? $model->descriptions : [], array_filter(\request('description', []), function ($value) {
            return $value !== null;
        }));

        $this->service->update($model, array_merge($request->except(['user_uuid']), [
            'content' => $content,
            'description' => $description
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
        foreach ($articleUuids as $articleUuid) {
            $model = $this->service->findOneById($articleUuid);
            $list_reason = $model->reject_reason;
            if ($request->get('publish_status') == Article::REJECT_PUBLISH_STATUS) {
                $list_reason[] = [
                    'content' => $request->get('reject_reason'),
                    'created_at' => Carbon::now()
                ];
            }
            $this->service->update($model, [
                'publish_status' => $request->get('publish_status'),
                'reject_reason' => $list_reason,
                'content_for_user' => $request->get('content_for_user') ?? $model->content_for_user,
            ]);
        }

        return $this->sendOkJsonResponse();
    }


    public function changeStatusMyArticle(ChangeStatusMyArticleRequest $request)
    {
        $articleUuids = $request->articles;
        foreach ($articleUuids as $articleUuid) {
            $user_uuid = $this->getUserUuid();
            $model = $this->service->findOneWhere([
                ['user_uuid', $user_uuid],
                ['app_id', auth()->appId()],
                ['uuid', $articleUuid]
            ]);
            if ($model) {
                $list_reason = $model->reject_reason;
                if ($request->get('publish_status') == Article::REJECT_PUBLISH_STATUS) {
                    $list_reason[] = [
                        'content' => $request->get('reject_reason'),
                        'created_at' => Carbon::now()
                    ];
                }
                $this->service->update($model, [
                    'publish_status' => $request->get('publish_status'),
                    'reject_reason' => $list_reason,
                    'content_for_user' => $request->get('content_for_user') ?? $model->content_for_user,
                ]);
            }
        }

        return $this->sendOkJsonResponse();
    }

    public function deleteMy($id)
    {
        $user_uuid = $this->getUserUuid();
        $model = $this->service->findOneWhereOrFail([
            ['user_uuid', $user_uuid],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);

        $model->delete();

        return $this->sendOkJsonResponse();
    }

    public function editorArticleChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $data = $this->service->editorArticleChart($groupBy, $startDate, $endDate);
        $total = $this->service->totalEditorArticleChart($startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $data,
            'total' => $total
        ]);
    }
}
