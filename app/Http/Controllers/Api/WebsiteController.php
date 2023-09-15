<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestMyDestroyTrait;
use App\Http\Controllers\Traits\RestMyShowTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\ChangeStatusMyWebsite;
use App\Http\Requests\ChangeStatusWebsite;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyWebsiteRequest;
use App\Http\Requests\UpdateMyWebsiteRequest;
use App\Http\Resources\WebsiteResource;
use App\Http\Resources\WebsiteResourceCollection;
use App\Models\Website;
use App\Services\MyWebsiteService;
use App\Services\WebsiteService;
use Techup\SiteController\Facades\SiteController;
use DB;

class WebsiteController extends AbstractRestAPIController
{
    use RestIndexTrait,
        RestShowTrait,
        RestDestroyTrait,
        RestIndexMyTrait,
        RestMyShowTrait,
        RestMyDestroyTrait;

    protected $myService;

    public function __construct(
        WebsiteService $service,
        MyWebsiteService $myService
    ) {
        $this->resourceClass = WebsiteResource::class;
        $this->resourceCollectionClass = WebsiteResourceCollection::class;
        $this->service = $service;
        $this->myService = $myService;
        $this->indexRequest = IndexRequest::class;
    }

    public function storeMy(MyWebsiteRequest $request)
    {
        $model = $this->myService->create(
            array_merge($request->all(), [
                "user_uuid" => auth()
                    ->user()
                    ->getKey(),
                "publish_status" => Website::PUBLISHED_PUBLISH_STATUS,
            ])
        );

        $model
            ->websitePages()
            ->attach(
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
            $request->except(["user_uuid", "publish_status"])
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
}
