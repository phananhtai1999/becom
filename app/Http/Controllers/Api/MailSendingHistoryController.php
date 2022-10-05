<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MailSendingHistoryRequest;
use App\Http\Requests\UpdateMailSendingHistoryRequest;
use App\Http\Resources\MailSendingHistoryResourceCollection;
use App\Http\Resources\MailSendingHistoryResource;
use App\Services\MailOpenTrackingService;
use App\Services\MailSendingHistoryService;
use App\Services\MyMailSendingHistoryService;
use Illuminate\Http\Request;

class MailSendingHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @var
     */
    protected $mailOpenTrackingService;

    /**
     * @param MailSendingHistoryService $service
     * @param MyMailSendingHistoryService $myService
     * @param MailOpenTrackingService $mailOpenTrackingService
     */
    public function __construct(
        MailSendingHistoryService $service,
        MyMailSendingHistoryService $myService,
        MailOpenTrackingService $mailOpenTrackingService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->mailOpenTrackingService = $mailOpenTrackingService;
        $this->resourceCollectionClass = MailSendingHistoryResourceCollection::class;
        $this->resourceClass = MailSendingHistoryResource::class;
        $this->storeRequest = MailSendingHistoryRequest::class;
        $this->editRequest = UpdateMailSendingHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyMailSendingHistory(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    $request->get('per_page', '15'),
                    $request->get('page', '1'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
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

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function mailOpenTracking(Request $request, $id)
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $this->mailOpenTrackingService->mailOpenTracking($id, $ip, $userAgent);

        $mailSendingHistory = $this->service->findOneById($id);
        $this->service->update($mailSendingHistory, ['status' => 'opened']);

        return response(file_get_contents(public_path('tracking_pixel/pixel.gif')))
            ->header('content-type', 'image/gif');
    }
}
