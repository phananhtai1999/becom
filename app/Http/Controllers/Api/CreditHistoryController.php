<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\CreditHistoryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateCreditHistoryRequest;
use App\Http\Resources\CreditHistoryResource;
use App\Http\Resources\CreditHistoryResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Services\CreditHistoryService;
use App\Services\MyCreditHistoryService;
use App\Services\MyUserCreditHistoryService;
use App\Services\UserCreditHistoryService;
use Illuminate\Support\Carbon;

class CreditHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexMyTrait;

    /**
     * @var MyCreditHistoryService
     */
    protected $myService;

    /**
     * @var
     */
    protected $campaignService;

    /**
     * @var
     */
    protected $userCreditHistoryService;

    /**
     * @var
     */
    protected $myAddCreditHistoryService;

    /**
     * @param CreditHistoryService $service
     * @param MyCreditHistoryService $myService
     * @param UserCreditHistoryService $userCreditHistoryService
     * @param MyUserCreditHistoryService $myAddCreditHistoryService
     */
    public function __construct(
        CreditHistoryService       $service,
        MyCreditHistoryService     $myService,
        UserCreditHistoryService   $userCreditHistoryService,
        MyUserCreditHistoryService $myAddCreditHistoryService
    )
    {
        $this->service = $service;
        $this->userCreditHistoryService = $userCreditHistoryService;
        $this->resourceCollectionClass = CreditHistoryResourceCollection::class;
        $this->resourceClass = CreditHistoryResource::class;
        $this->storeRequest = CreditHistoryRequest::class;
        $this->editRequest = UpdateCreditHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->myService = $myService;
        $this->myAddCreditHistoryService = $myAddCreditHistoryService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $data = array_merge($request->all(), [
            'user_uuid' => $request->user_uuid ?: auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

        $model = $this->service->create($data);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyCreditHistory($id)
    {
        $model = $this->myService->showMyCreditHistory($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param ChartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function creditChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');
        $totalCreditAdded = $this->userCreditHistoryService->totalCreditAdded($startDate, $endDate);
        $totalCreditUsed = $this->service->totalCreditUsed($startDate, $endDate);
        $data = $this->service->creditChart($groupBy, $startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $data,
            'total' => [
                'add' => (int)$totalCreditAdded,
                'used' => (int)$totalCreditUsed,
            ]
        ]);
    }

    /**
     * @param ChartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCreditChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');
        $totalMyCreditAdded = $this->myAddCreditHistoryService->myTotalCreditAdded($startDate, $endDate);
        $totalMyCreditUsed = $this->myService->myTotalCreditUsed($startDate, $endDate);
        $data = $this->myService->myCreditChart($groupBy, $startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $data,
            'total' => [
                'add' => (int)$totalMyCreditAdded,
                'used' => (int)$totalMyCreditUsed,
            ]
        ]);
    }
}
