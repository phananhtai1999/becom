<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\MailSendingHistoryRequest;
use App\Http\Requests\UpdateMailSendingHistoryRequest;
use App\Http\Resources\MailSendingHistoryResourceCollection;
use App\Http\Resources\MailSendingHistoryResource;
use App\Services\MailSendingHistoryService;
use App\Services\MyMailSendingHistoryService;

class MailSendingHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @param MailSendingHistoryService $service
     * @param MyMailSendingHistoryService $myService
     */
    public function __construct(
        MailSendingHistoryService $service,
        MyMailSendingHistoryService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = MailSendingHistoryResourceCollection::class;
        $this->resourceClass = MailSendingHistoryResource::class;
        $this->storeRequest = MailSendingHistoryRequest::class;
        $this->editRequest = UpdateMailSendingHistoryRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyMailSendingHistory()
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    request()->get('per_page', '15'),
                    request()->get('page', '1'),
                    request()->get('columns', '*'),
                    request()->get('page_name', 'page')
                )
            )
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyMailSendingHistory($id)
    {
        $model = $this->myService->findMyMailSendingHistoryByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
