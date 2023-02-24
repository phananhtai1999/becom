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
        $sort = explode(',', $request->sort);
        $arraySort = [
            'uuid',
            'user_uuid',
            'credit',
            'campaign_uuid',
            'add_by_uuid',
            '-uuid',
            '-user_uuid',
            '-credit',
            '-campaign_uuid',
            '-add_by_uuid',
        ];
        $directionDesc = [
            '-uuid',
            '-user_uuid',
            '-credit',
            '-campaign_uuid',
            '-add_by_uuid',
            '-created_at'
        ];
        $fieldSort = !empty($sort[0]) && in_array($sort[0], $arraySort) ? $sort[0] : '-created_at';
        $orderBy = in_array($fieldSort, $directionDesc) ? 'desc' : 'asc';
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
                $fieldSort,
                $orderBy,
                count($filters),
                $request->get('per_page', '15'),
                $request->get('columns', '*'),
                $request->get('page_name', 'page'),
                $request->get('page', '1'),
                $request->search,
                $request->search_by
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
        $sort = explode(',', $request->sort);
        $arraySort = [
            'uuid',
            'user_uuid',
            'credit',
            'campaign_uuid',
            'add_by_uuid',
            '-uuid',
            '-user_uuid',
            '-credit',
            '-campaign_uuid',
            '-add_by_uuid',
        ];
        $directionDesc = [
            '-uuid',
            '-user_uuid',
            '-credit',
            '-campaign_uuid',
            '-add_by_uuid',
            '-created_at'
        ];
        $fieldSort = !empty($sort[0]) && in_array($sort[0], $arraySort) ? $sort[0] : '-created_at';
        $orderBy = in_array($fieldSort, $directionDesc) ? 'desc' : 'asc';

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
                $fieldSort,
                $orderBy,
                count($filters),
                $request->get('per_page', '15'),
                $request->get('columns', '*'),
                $request->get('page_name', 'page'),
                $request->get('page', '1'),
                $request->search,
                $request->search_by
            );

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData($this->resourceCollectionClass, $filterSendType)
            );
        }
        $myAddAndUseCreditTransactionHistory = $this->myService->myFilterAddAndUseCreditTransactionHistory(
            $fieldSort,
            $orderBy,
            $request->get('per_page', '15'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
            $request->get('page', '1'),
            $request->search,
            $request->search_by
        );

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $myAddAndUseCreditTransactionHistory)
        );
    }
}
