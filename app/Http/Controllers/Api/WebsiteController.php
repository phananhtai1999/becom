<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\MyWebsiteRequest;
use App\Http\Requests\UpdateMyWebsiteRequest;
use App\Http\Requests\UpdateWebsiteRequest;
use App\Http\Requests\WebsiteRequest;
use App\Http\Resources\WebsiteCollection;
use App\Http\Resources\WebsiteResource;
use App\Services\WebsiteService;

class WebsiteController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @param WebsiteService $service
     */
    public function __construct(WebsiteService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = WebsiteCollection::class;
        $this->resourceClass = WebsiteResource::class;
        $this->storeRequest = WebsiteRequest::class;
        $this->editRequest = UpdateWebsiteRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyWebsite()
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->service->indexMyWebsite(request()->get('per_page', 15))
            )
        );
    }

    /**
     * @param MyWebsiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyWebsite(MyWebsiteRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyWebsite($id)
    {
        $model = $this->service->showMyWebsite($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyWebsiteRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyWebsite(UpdateMyWebsiteRequest $request, $id)
    {
        $model = $this->service->findOrFailById($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyWebsite($id)
    {
        $this->service->deleteMyWebsite($id);

        return $this->sendOkJsonResponse();
    }
}
