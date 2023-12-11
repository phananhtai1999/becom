<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestMyDestroyTrait;
use App\Http\Controllers\Traits\RestMyShowTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\Article\ChangeStatusArticleRequest;
use App\Http\Requests\ChangeStatusDefaultWebsiteRequest;
use App\Http\Requests\ChangeStatusMyWebsite;
use App\Http\Requests\ChangeStatusWebsite;
use App\Http\Requests\ChangeStatusWebsiteRequest;
use App\Http\Requests\CopyDefaultWebsiteRequest;
use App\Http\Requests\GetWebsitePagesRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyWebsiteRequest;
use App\Http\Requests\UnpublishedWebsiteRequest;
use App\Http\Requests\UpdateMyWebsiteRequest;
use App\Http\Requests\UpdateUnpublishedWebsiteRequest;
use App\Http\Requests\UpdateWebsiteRequest;
use App\Http\Requests\WebsiteRequest;
use App\Http\Resources\WebsiteResource;
use App\Http\Resources\WebsiteResourceCollection;
use App\Models\Article;
use App\Models\Role;
use App\Models\SectionTemplate;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Services\MyWebsiteService;
use App\Services\SectionTemplateService;
use App\Services\WebsitePageService;
use App\Services\WebsiteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Techup\SiteController\Facades\SiteController;

class WebsiteController extends AbstractRestAPIController
{
    use RestIndexTrait,
        RestShowTrait,
        RestDestroyTrait,
        RestIndexMyTrait,
        RestMyShowTrait,
        RestMyDestroyTrait;

    protected $myService;

    /**
     * @var SectionTemplateService
     */
    protected $sectionTemplateService;

    protected $websitePageService;

    public function __construct(
        WebsiteService   $service,
        MyWebsiteService $myService,
        SectionTemplateService $sectionTemplateService,
        WebsitePageService $websitePageService
    )
    {
        $this->resourceClass = WebsiteResource::class;
        $this->resourceCollectionClass = WebsiteResourceCollection::class;
        $this->service = $service;
        $this->myService = $myService;
        $this->indexRequest = IndexRequest::class;
        $this->sectionTemplateService = $sectionTemplateService;
        $this->websitePageService = $websitePageService;
    }

    public function store(WebsiteRequest $request)
    {
        $model = $this->myService->create(
            array_merge($request->all(), [
                "user_uuid" => $request->get('user_uuid') ?? auth()->user()->getKey(),
                'is_default' => true,
            ])
        );

        $model->websitePages()->attach($this->getWebsitePagesByRequest($request->get("website_pages", [])));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function edit($id, UpdateWebsiteRequest $request)
    {
        $model = $this->service->findOrFailById($id);

        $this->myService->update($model, $request->all());

        $model
            ->websitePages()
            ->sync(
                $this->getWebsitePagesByRequest(
                    $request->get("website_pages", [])
                )
            );

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function storeMy(MyWebsiteRequest $request)
    {
        $model = $this->myService->create(
            array_merge($request->all(), [
                "user_uuid" => auth()->user()->getKey(),
                'is_default' => false,
            ])
        );

        $model->websitePages()->attach(
                $this->getWebsitePagesByRequest(
                    $request->get("website_pages", [])
                )
            );

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function editMy($id, UpdateMyWebsiteRequest $request)
    {
        $model = $this->myService->showMyWebsite($id);

        $this->myService->update(
            $model,
            $request->except(["user_uuid"])
        );

        $model
            ->websitePages()
            ->sync(
                $this->getWebsitePagesByRequest(
                    $request->get("website_pages", [])
                )
            );

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function getWebsitePagesByRequest($webpages)
    {
        return collect($webpages)->map(function ($webpage) {
            return [
                "website_page_uuid" => $webpage["uuid"],
                "is_homepage" => $webpage["is_homepage"] ?? 0,
                "ordering" => $webpage["ordering"],
            ];
        });
    }

    public function changeStatus(ChangeStatusWebsite $request)
    {
        $this->changeStatusWebsiteByRequest($request);
        return $this->sendOkJsonResponse();
    }

    public function changeStatusMyWebsite(ChangeStatusMyWebsite $request)
    {
        $this->changeStatusWebsiteByRequest($request);
        return $this->sendOkJsonResponse();
    }

    public function changeStatusWebsiteByRequest($request)
    {
        $websiteUuids = $request->websites;
        foreach ($websiteUuids as $websiteUuid) {
            DB::beginTransaction();
            try {
                $website = $this->service->findOneById($websiteUuid);
                if (
                    $request->get("publish_status") ==
                    Website::PUBLISHED_PUBLISH_STATUS
                ) {
                    SiteController::postDeployments(
                        $website->domain->name,
                        $websiteUuid
                    );
                }

                $this->changeStatusTemplateByStatusWebsite($website, $request->get("publish_status"));
                $this->service->update($website, [
                    "publish_status" => $request->get("publish_status"),
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }

    public function publicWebsiteByDomainAndPublishStatus(IndexRequest $request)
    {
        $model = $this->service->publicWebsiteByDomainAndPublishStatus($request->domain_name);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function storeUnpublishedWebsite(UnpublishedWebsiteRequest $request)
    {
        $model = $this->myService->create(
            array_merge([
                "user_uuid" => auth()->user()->getKey(),
                'is_default' => true,
            ], $request->all())
        );

        $model->websitePages()->attach($this->getWebsitePagesByRequest($request->get("website_pages", [])));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function indexUnpublishedWebsite(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => Article::PENDING_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function editUnpublishedWebsite($id, UpdateUnpublishedWebsiteRequest $request)
    {
        $model = $this->myService->showMyWebsite($id);

        $this->myService->update(
            $model,
            $request->except(["user_uuid"])
        );

        $model
            ->websitePages()
            ->sync(
                $this->getWebsitePagesByRequest(
                    $request->get("website_pages", [])
                )
            );

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function showUnpublishedWebsite($id)
    {
        $model = $this->service->findOneWhereOrFail([
            'user_uuid' => auth()->user()->getkey(),
            'publish_status' => Article::PENDING_PUBLISH_STATUS,
            'uuid' => $id
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param ChangeStatusWebsiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function changeStatusWebsite(ChangeStatusWebsiteRequest $request)
    {
        $this->changeStatusWebsiteByRequest($request);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param ChangeStatusWebsiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function defaultWebsites(IndexRequest $request)
    {
        if (auth()->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
            $models = $this->service->getDefaultWebsiteForAdmin($request);
        } else {
            $models = $this->service->getCollectionWithPaginationByCondition($request, [
                'domain_uuid' => null,
                'publish_status' => Website::PUBLISHED_PUBLISH_STATUS,
            ]);
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function changeStatusDefaultWebsite(ChangeStatusDefaultWebsiteRequest $request)
    {
        foreach ($request->websites as $websiteUuid) {
            $website = $this->service->findOneById($websiteUuid);
            $this->changeStatusTemplateByStatusWebsite($website, $request->get("publish_status"));
            $this->service->update($website, [
                "publish_status" => $request->get("publish_status"),
            ]);
        }

        return $this->sendOkJsonResponse();
    }

    public function changeStatusTemplateByStatusWebsite($website, $publicStatus)
    {
        if (in_array($publicStatus, [Website::PUBLISHED_PUBLISH_STATUS, Website::PENDING_PUBLISH_STATUS])){
            $statusWebsitePage = $publicStatus == Website::PUBLISHED_PUBLISH_STATUS
                ? WebsitePage::PUBLISHED_PUBLISH_STATUS : WebsitePage::PENDING_PUBLISH_STATUS;
            $website->websitePages()
                ->where('publish_status', '<>', $statusWebsitePage)->get()->map(function ($websitePage) use ($statusWebsitePage){
                    $this->service->update($websitePage, ["publish_status" => $statusWebsitePage]);
                });

            $statusSectionTemplate = $publicStatus == Website::PUBLISHED_PUBLISH_STATUS
                ? SectionTemplate::PUBLISHED_PUBLISH_STATUS : SectionTemplate::PENDING_PUBLISH_STATUS;
            $headerSection = $website->headerSection;
            $footerSection = $website->footerSection;
            if ($headerSection && $headerSection->publish_status != $statusSectionTemplate){
                $this->service->update($headerSection, ["publish_status" => $statusWebsitePage]);
            }
            if ($footerSection && $footerSection->publish_status != $statusSectionTemplate){
                $this->service->update($footerSection, ["publish_status" => $statusWebsitePage]);
            }
        }
    }

    public function copyDefaultWebsite($id, CopyDefaultWebsiteRequest $request)
    {
        $copyWebsite = $this->service->showCopyWebsiteByUuid($id);

        if($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()){
            $statusTemplate = SectionTemplate::PUBLISHED_PUBLISH_STATUS;
            $statusWebsite = $request->get('publish_status');
            $isDefault = true;
        }elseif($this->user()->roles->whereIn('slug', [Role::ROLE_EDITOR])->count()){
            $statusTemplate = $request->get('publish_status') == Website::PENDING_PUBLISH_STATUS
                ? SectionTemplate::PENDING_PUBLISH_STATUS : SectionTemplate::DRAFT_PUBLISH_STATUS;
            $statusWebsite = $request->get('publish_status');
            $isDefault = true;
        }else{
            $statusTemplate = SectionTemplate::PUBLISHED_PUBLISH_STATUS;
            $statusWebsite = $request->get('publish_status');
            $isDefault = false;
        }

        DB::beginTransaction();
        try{
            $headerWebsite = $this->sectionTemplateService->create(array_merge($copyWebsite->headerSection->toArray(), [
                "user_uuid"=> auth()->user()->getKey(),
                'publish_status' => $statusTemplate,
                "is_default" => $isDefault
            ]));
            $footerWebsite = $this->sectionTemplateService->create(array_merge($copyWebsite->footerSection->toArray(), [
                "user_uuid"=> auth()->user()->getKey(),
                'publish_status' => $statusTemplate,
                "is_default" => $isDefault
            ]));

            $website = $this->service->create(array_merge($request->all(), [
                'header_section_uuid' => $headerWebsite->uuid,
                'footer_section_uuid' => $footerWebsite->uuid,
                'user_uuid' => auth()->user()->getKey(),
                'publish_status' => $statusWebsite,
            ]));

            $websitePages = $copyWebsite->websitePages->map(function ($item) use ($statusTemplate, $isDefault){
                $websitePage = $this->websitePageService->create(array_merge($item->toArray(), [
                    'user_uuid' => auth()->user()->getKey(),
                    'is_default' => $isDefault,
                    'publish_status' => $statusTemplate
                ]));
                $pivot = $item->pivot->toArray();
                return [
                    "website_page_uuid" => $websitePage->uuid,
                    "is_homepage" => $pivot['is_homepage'],
                    "ordering" => $pivot['ordering']
                ];
            });

            $website->websitePages()->sync($websitePages);

            DB::commit();
            return $this->sendCreatedJsonResponse(
                $this->service->resourceToData($this->resourceClass, $website)
            );
        }catch (\Exception $exception){
            DB::rollback();
            throw $exception;
        }
    }

    public function toggleNewsPage($id) {
        $website = $this->service->findOrFailById($id);
        $website->update(['is_active_news_page' => !$website->is_active_news_page]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $website)
        );
    }
}
