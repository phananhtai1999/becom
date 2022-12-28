<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\CreditTransactionHistoryResource;
use App\Http\Resources\CreditTransactionHistoryResourceCollection;
use App\Services\CreditTransactionHistoryService;
use App\Services\MyCreditTransactionHistoryService;

class CreditTransactionHistoryController extends AbstractRestAPIController
{
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
    public function index(IndexRequest $request)
    {
        $filters = $request->filter;
        if (!empty($filters)) {
            foreach ($filters as $key => $filter) {
                if ($filter == null) {
                    unset($filters[$key]);
                }
            }
        }
        if (!empty($filters['campaign.send_type'])) {
            $filterSendType = $this->service->customFilterSendTypeOnCampaign(
                $filters,
                count($filters),
                $request->get('per_page', '15'),
                $request->get('columns', '*'),
                $request->get('page_name', 'page'),
                $request->get('page', '1'),
            );

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData($this->resourceCollectionClass, $filterSendType)
            );
        }
        $models = $this->service->getCollectionWithPagination();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyCreditTransactionHistoryView(IndexRequest $request)
    {
        $filters = $request->filter;
        if (!empty($filters)) {
            foreach ($filters as $key => $filter) {
                if ($filter == null) {
                    unset($filters[$key]);
                }
            }
        }
        if (!empty($filters['campaign.send_type'])) {
            $filterSendType = $this->myService->customMyFilterSendTypeOnCampaign(
                $filters,
                count($filters),
                $request->get('per_page', '15'),
                $request->get('columns', '*'),
                $request->get('page_name', 'page'),
                $request->get('page', '1'),
            );

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData($this->resourceCollectionClass, $filterSendType)
            );
        }
        $myAddAndUseCreditTransactionHistory = $this->myService->myFilterAddAndUseCreditTransactionHistory(
            $request->get('per_page', '15'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
            $request->get('page', '1'),
        );

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $myAddAndUseCreditTransactionHistory)
        );
    }
}
