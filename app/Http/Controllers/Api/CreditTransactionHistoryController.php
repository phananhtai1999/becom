<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\CreditTransactionHistoryResource;
use App\Http\Resources\CreditTransactionHistoryResourceCollection;
use App\Services\CreditTransactionHistoryService;
use App\Services\MyCreditTransactionHistoryService;

class CreditTransactionHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @param CreditTransactionHistoryService $service
     * @param MyCreditTransactionHistoryService $myService
     */
    public function __construct(
        CreditTransactionHistoryService $service,
        MyCreditTransactionHistoryService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = CreditTransactionHistoryResourceCollection::class;
        $this->resourceClass = CreditTransactionHistoryResource::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyCreditTransactionHistoryView(IndexRequest $request)
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
}
