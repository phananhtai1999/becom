<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\ConfigShortcodeRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateWebsitePageShortCodeRequest;
use App\Http\Requests\WebsitePageShortCodeRequest;
use App\Http\Resources\WebsitePageShortCodeResource;
use App\Http\Resources\WebsitePageShortCodeResourceCollection;
use App\Services\ShortCodeGroupService;
use App\Services\WebsitePageShortCodeService;

class WebsitePageShortCodeController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    public function __construct(WebsitePageShortCodeService $service, ShortCodeGroupService $shortCodeGroupService)
    {
        $this->service = $service;
        $this->shortCodeGroupService = $shortCodeGroupService;
        $this->resourceCollectionClass = WebsitePageShortCodeResourceCollection::class;
        $this->resourceClass = WebsitePageShortCodeResource::class;
        $this->storeRequest = WebsitePageShortCodeRequest::class;
        $this->editRequest = UpdateWebsitePageShortCodeRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    public function configShortcode(ConfigShortcodeRequest $request)
    {
        $shortCode = $this->shortCodeGroupService->findOneWhere(['key' => $request->get('type')]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $shortCode->shortCodes)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus($id)
    {
        $shortCode = $this->service->findOrFailById($id);
        $shortCode->update(['status' => !$shortCode->status]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $shortCode)
        );
    }
}
