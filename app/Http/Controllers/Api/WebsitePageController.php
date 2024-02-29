<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Requests\AcceptPublishWebsitePageRequest;
use App\Http\Requests\ConfigShortcodeRequest;
use App\Http\Requests\GetInfoByDomainUrlRequest;
use App\Http\Requests\GetWebsitePagesRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyWebsitePageRequest;
use App\Http\Requests\ShowWebsitePageRequest;
use App\Http\Requests\UnpublishedWebsitePageRequest;
use App\Http\Requests\UpdateMyWebsitePageRequest;
use App\Http\Requests\UpdateUnpublishedWebsitePageRequest;
use App\Http\Requests\UpdateWebsitePageRequest;
use App\Http\Requests\WebsitePageRequest;
use App\Http\Resources\ArticleResourceCollection;
use App\Http\Resources\WebsitePageResource;
use App\Http\Resources\WebsitePageResourceCollection;
use App\Models\ArticleCategory;
use App\Models\WebsitePage;
use App\Services\ArticleCategoryService;
use App\Services\ArticleService;
use App\Services\DomainService;
use Techup\ApiConfig\Services\LanguageService;
use App\Services\MyWebsitePageService;
use App\Services\ShopService;
use App\Services\WebsitePageService;
use App\Services\WebsitePageShortCodeService;
use App\Services\WebsiteService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WebsitePageController extends AbstractRestAPIController
{
    use RestIndexTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var MyWebsitePageService
     */
    protected $myService;

    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @param WebsitePageService $service
     * @param MyWebsitePageService $myService
     * @param LanguageService $languageService
     */
    public function __construct(
        WebsitePageService     $service,
        MyWebsitePageService   $myService,
        LanguageService        $languageService,
        ArticleService         $articleService,
        ArticleCategoryService $articleCategoryService,
        DomainService          $domainService,
        WebsiteService         $websiteService,
        ShopService $shopService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->languageService = $languageService;
        $this->articleService = $articleService;
        $this->articleCategoryService = $articleCategoryService;
        $this->domainService = $domainService;
        $this->websiteService = $websiteService;
        $this->shopService = $shopService;
        $this->resourceCollectionClass = WebsitePageResourceCollection::class;
        $this->resourceClass = WebsitePageResource::class;
        $this->indexRequest = IndexRequest::class;
        $this->storeRequest = WebsitePageRequest::class;
        $this->editRequest = UpdateWebsitePageRequest::class;
    }

    public function show(IndexRequest $request, $id)
    {
        $model = $this->myService->findOneWhereOrFail($request->publish_status ?
            [['publish_status', $request->publish_status], ['uuid', $id]]
            : [['uuid', $id]]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function getWebsitePage(ShowWebsitePageRequest $request, $id)
    {
        if(auth()->userId()) {
            $websitePage = $this->myService->findOneWhereOrFail([['uuid', $id], ['user_uuid', auth()->userId()]]);
        } else {
            $websitePage = $this->myService->findOneWhereOrFail([['publish_status', WebsitePage::PUBLISHED_PUBLISH_STATUS], ['uuid', $id]]);
        }

        $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        if ($websitePage->type == WebsitePage::ARTICLE_DETAIL_TYPE) {
            if ($request->get('article_slug')) {
                $article = $this->articleService->findOneWhereOrFail(['slug' => $request->get('article_slug')]);
            } elseif ($request->get('article_uuid')) {
                $article = $this->articleService->findOrFailById($request->get('article_uuid'));
            } else {
                $article = $this->articleService->getLastArticle();
            }
            if ($request->get('replace_column') == 'json') {
                $websitePage = $this->service->renderContentForJson($websitePage, $article);
            } else {
                $websitePage = $this->service->renderContent($websitePage, $article);
            }
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::ARTICLE_CATEGORY_TYPE) {
            if ($request->get('article_category_slug')) {
                $articleCategory = $this->articleCategoryService->findOneWhereOrFail(['slug' => $request->get('article_category_slug')]);
            } elseif ($request->get('article_category_uuid')) {
                $articleCategory = $this->articleCategoryService->findOrFailById($request->get('article_category_uuid'));
            } else {
                $articleCategory = $this->articleCategoryService->getLastArticleCategory();
            }
            if ($request->get('replace_column') == 'json') {
                $websitePage = $this->service->renderContentForArticleCategoryJson($websitePage, $articleCategory);
            } else {
                $websitePage = $this->service->renderContentForArticleCategory($websitePage, $articleCategory);
            }
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::HOME_ARTICLES_TYPE) {
            $websitePage = $this->service->renderContentForHomeArticles($websitePage);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::NEWS_HEADER_TYPE) {
            $websitePage = $this->service->renderContentForNewsHeader($websitePage);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        }

        //product webpage
        if ($websitePage->type == WebsitePage::PRODUCT_DETAIL_TYPE) {
            $productDetailData = $this->shopService->getProductDetailData($request->product_uuid);
            $websitePage = $this->service->renderContentForProductDetail($websitePage, $productDetailData['data']);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::PRODUCT_CATEGORY_TYPE) {
            $productCategoryData = $this->shopService->getProductCategoryData($request->get('product_category_slug'));
            $websitePage = $this->service->renderContentForProductCategory($websitePage, $productCategoryData);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::HOME_PRODUCTS_TYPE) {
            $websitePage = $this->service->renderContentForHomeProducts($websitePage);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::PRODUCT_HEADER_TYPE) {
            $websitePage = $this->service->renderContentForProductHeader($websitePage);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        }

        return $response;
    }

    public function getProductWebsitePage(ShowWebsitePageRequest $request, $id)
    {
        $websitePage = $this->myService->findOneWhereOrFail($request->publish_status ?
            [['publish_status', $request->publish_status], ['uuid', $id]]
            : [['uuid', $id]]);
        $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        if ($websitePage->type == WebsitePage::PRODUCT_DETAIL_TYPE) {
            $productDetailData = $this->shopService->getProductDetailData($request->product_uuid);
            $websitePage = $this->service->renderContentForProductDetail($websitePage, $productDetailData['data']);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::PRODUCT_CATEGORY_TYPE) {
            $productCategoryData = $this->shopService->getProductCategoryData($request->get('product_category_slug'));
            $websitePage = $this->service->renderContentForProductCategory($websitePage, $productCategoryData);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::HOME_PRODUCTS_TYPE) {
            $websitePage = $this->service->renderContentForHomeProducts($websitePage);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        } elseif ($websitePage->type == WebsitePage::PRODUCT_HEADER_TYPE) {
            $websitePage = $this->service->renderContentForProductHeader($websitePage);
            $response = $this->sendOkJsonResponse(['data' => $websitePage]);
        }

        return $response;
    }


    public function getWebsitePageWithReplace(GetWebsitePagesRequest $request)
    {
        if ($request->get('website_page_slug')) {
            $websitePage = $this->service->getWebsitePageByDomainAndWebsitePageSlug($request->get('domain'), $request->get('website_page_slug'));
        } else {
            $websitePage = $this->service->getWebsitePageByWebsiteAndWebsitePageUuid($request->get('website_uuid'), $request->get('website_page_uuid'));
        }

        if(in_array($websitePage->type, app(WebsitePage::class)->getTypeNewsWebsitePage())) {
            if (!$request->get('article_slug') && !$request->get('article_uuid')
                && !$request->get('article_category_slug') && !$request->get('article_category_uuid')) {
                $websitePage = $this->service->renderContentForHomeArticles($websitePage);
            } else {
                if (!$request->get('domain')) {
                    $newsWebsitePages = $this->service->getNewsWebsitePagesByWebsite($request->get('website_uuid'));
                } else {
                    $newsWebsitePages = $this->service->getNewsWebsitePagesByDomain($request->get('domain'));
                }
                if (($request->get('article_uuid') || $request->get('article_slug'))
                    && ($request->get('article_category_uuid') || $request->get('article_category_slug'))) {
                    $websitePage = $newsWebsitePages->where('type', WebsitePage::ARTICLE_DETAIL_TYPE)->first();
                    if ($request->get('article_uuid')) {
                        $article = $this->articleService->findOrFailById($request->get('article_uuid'));
                    } else {
                        $article = $this->articleService->findOneWhereOrFail(['slug' => $request->get('article_slug')]);
                    }
                    $websitePage = $this->service->renderContent($websitePage, $article);

                } elseif (!($request->get('article_uuid') || $request->get('article_slug'))
                    && ($request->get('article_category_uuid') || $request->get('article_category_slug'))) {
                    $websitePage = $newsWebsitePages->where('type', WebsitePage::ARTICLE_CATEGORY_TYPE)->first();
                    if ($request->get('article_category_uuid')) {
                        $articleCategory = $this->articleCategoryService->findOrFailById($request->get('article_category_uuid'));
                    } else {
                        $articleCategory = $this->articleCategoryService->findOneWhereOrFail(['slug' => $request->get('article_category_slug')]);
                    }
                    $websitePage = $this->service->renderContentForArticleCategory($websitePage, $articleCategory);
                }
            }
        } else {
            $websitePage = $this->replaceWebsitePageProduct($request, $websitePage);
        }


        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $websitePage)
        );
    }

    public function replaceWebsitePageProduct($request, $websitePage) {
        if (!$request->get('product_slug') && !$request->get('product_uuid')
            && !$request->get('product_category_slug') && !$request->get('product_category_uuid')) {
            $websitePage = $this->service->renderContentForHomeProducts($websitePage);
        } else {
            if (!$request->get('domain')) {
                $productWebsitePages = $this->service->getProductWebsitePagesByWebsite($request->get('website_uuid'));
            } else {
                $productWebsitePages = $this->service->getProductWebsitePagesByDomain($request->get('domain'));
            }
            if (($request->get('product_uuid') || $request->get('product_slug'))
                && ($request->get('product_category_uuid') || $request->get('product_category_slug'))) {
                $websitePage = $productWebsitePages->where('type', WebsitePage::PRODUCT_DETAIL_TYPE)->first();
                try {
                    if ($request->get('product_uuid')) {
                        $productDetailData = $this->shopService->getProductDetailData($request->product_uuid);
                    } else {
                        $productDetailData = $this->shopService->getProductDetailData(null, $request->get('product_slug'));
                    }
                    $websitePage = $this->service->renderContentForProductDetail($websitePage, $productDetailData['data']);
                } catch (\Exception $exception) {
                }

            } elseif (!($request->get('product_uuid') || $request->get('product_slug'))
                && ($request->get('product_category_uuid') || $request->get('product_category_slug'))) {
                $websitePage = $productWebsitePages->where('type', WebsitePage::PRODUCT_CATEGORY_TYPE)->first();
                if ($request->get('product_category_uuid')) {
                    $productCategoryData = $this->shopService->getProductCategoryData(null, $request->get('product_category_uuid'));
                } else {
                    $productCategoryData = $this->shopService->getProductCategoryData($request->get('product_category_slug'));
                }
                if (!empty($productCategoryData['category'])) {
                    $websitePage = $this->service->renderContentForProductCategory($websitePage, $productCategoryData);
                }
            }
        }

        return $websitePage;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => WebsitePage::PUBLISHED_PUBLISH_STATUS,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'description' => $request->keyword ? array_merge($request->keyword, $request->description ?? $request->keyword) : $request->description
        ]));

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
        $model = $this->service->findOrFailById($id);

        $request = app($this->editRequest);

        if (($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        //Generate description by keyword and value lang != null
        $description = array_merge(\request('keyword', []), !empty($model->descriptions) ? $model->descriptions : [], array_filter(\request('description', []), function ($value) {
            return $value !== null;
        }));
        $this->service->update($model, array_merge($request->except(['user_uuid']), [
            'description' => $description
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param MyWebsitePageRequest $request
     * @return JsonResponse
     */
    public function storeMyWebsitePage(MyWebsitePageRequest $request)
    {
        if (($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'is_default' => false,
            'description' => $request->keyword ? array_merge($request->keyword, $request->description ?? $request->keyword) : $request->description
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMyWebsitePage($id)
    {
        $model = $this->myService->showMyWebsitePageByUuid($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyWebsitePageRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMyWebsitePage(UpdateMyWebsitePageRequest $request, $id)
    {
        $model = $this->myService->showMyWebsitePageByUuid($id);

        if (($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        //Generate description by keyword and value lang != null
        $description = array_merge(\request('keyword', []), !empty($model->descriptions) ? $model->descriptions : [], array_filter(\request('description', []), function ($value) {
            return $value !== null;
        }));
        $this->service->update($model, array_merge($request->except(['user_uuid', 'is_default']), [
            'description' => $description
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMyWebsitePage($id)
    {
        $this->myService->deleteMyWebsitePageByUuid($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexUnpublishedWebsitePage(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => WebsitePage::PENDING_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUnpublishedWebsitePage($id)
    {
        $model = $this->service->showWebsitePageForEditorById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UnpublishedWebsitePageRequest $request
     * @return JsonResponse
     */
    public function storeUnpublishedWebsitePage(UnpublishedWebsitePageRequest $request)
    {
        if (($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'description' => $request->keyword ? array_merge($request->keyword, $request->description ?? $request->keyword) : $request->description
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateUnpublishedWebsitePageRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editUnpublishedWebsitePage(UpdateUnpublishedWebsitePageRequest $request, $id)
    {
        $model = $this->service->showWebsitePageForEditorById($id);

        if (($request->keyword && !$this->languageService->checkLanguages($request->keyword))
            || ($request->description && !$this->languageService->checkLanguages($request->description))) {

            return $this->sendValidationFailedJsonResponse();
        }

        //Generate description by keyword and value lang != null
        $description = array_merge(\request('keyword', []), !empty($model->descriptions) ? $model->descriptions : [], array_filter(\request('description', []), function ($value) {
            return $value !== null;
        }));
        $this->service->update($model, array_merge($request->except(['user_uuid']), [
            'description' => $description
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param AcceptPublishWebsitePageRequest $request
     * @return JsonResponse
     */
    public function changeStatusWebsitePage(AcceptPublishWebsitePageRequest $request)
    {
        $websitePageUuids = $request->website_pages;
        foreach ($websitePageUuids as $websitePageUuid) {
            $model = $this->service->findOneById($websitePageUuid);
            $list_reason = $model->reject_reason;
            if ($request->get('publish_status') == WebsitePage::REJECT_PUBLISH_STATUS) {
                $list_reason[] = [
                    'content' => $request->get('reject_reason'),
                    'created_at' => Carbon::now()
                ];
            }
            $this->service->update($model, [
                'publish_status' => $request->get('publish_status'),
                'reject_reason' => $list_reason
            ]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWebsitePagesDefault(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['publish_status', WebsitePage::PUBLISHED_PUBLISH_STATUS],
            ['is_default', true],
        ]);
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showWebsitePagesDefault($id)
    {
        $model = $this->service->findOneWhereOrFail([
            ['publish_status', WebsitePage::PUBLISHED_PUBLISH_STATUS],
            ['is_default', true],
            ['uuid', $id]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function publicWebsitePageByDomainAndSlug(IndexRequest $request)
    {
        $model = $this->service->publicWebsitePageByDomainAndSlug($request->domain_name, $request->slug);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function listMyAcceptedWebsitePages(IndexRequest $request)
    {
        $models = $this->myService->getIsCanUseWebsitePages($request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function listAcceptedWebsitePages(IndexRequest $request)
    {
        $models = $this->service->getIsCanUseWebsitePages($request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param GetInfoByDomainUrlRequest $request
     * @return JsonResponse
     */
    public function getInfoByDomainUrl(GetInfoByDomainUrlRequest $request)
    {
        if ($request->get('article_category_slug')){
            $articleCategory = $this->articleCategoryService->findOneWhere(['slug' => $request->get('article_category_slug')]);
        }
        if ($request->get('article_slug')){
            $article = $this->articleService->findOneWhere(['slug' => $request->get('article_slug')]);
        }
        if ($request->get('website_page_slug')){
            $websitePage = $this->service->getWebsitePageByDomainAndWebsitePageSlug($request->get('domain'), $request->get('website_page_slug'));
        }
        $data = [
            'article_category' => $articleCategory ?? null,
            'article' => $article ?? null,
            'website_page' => $websitePage ?? null
        ];

        return $this->sendOkJsonResponse(['data' => $data]);
    }
}
