<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\DashboardPartnerChartRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerTrackingRequest;
use App\Http\Requests\UpdatePartnerTrackingRequest;
use App\Http\Resources\PartnerTrackingResource;
use App\Http\Resources\PartnerTrackingResourceCollection;
use App\Services\PartnerService;
use App\Services\PartnerTrackingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PartnerTrackingController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestStoreTrait,RestEditTrait, RestDestroyTrait;

    protected $partnerService;
    /**
     * @param PartnerTrackingService $service
     */
    public function __construct(
        PartnerTrackingService  $service,
        PartnerService $partnerService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerTrackingResourceCollection::class;
        $this->resourceClass = PartnerTrackingResource::class;
        $this->storeRequest = PartnerTrackingRequest::class;
        $this->editRequest = UpdatePartnerTrackingRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->partnerService = $partnerService;
    }

    public function trackingInvitePartner(Request $request)
    {
        $partner = $this->partnerService->findOneWhereOrFail(['code' => $request->get('code')]);
        //Kiểm tra có cookie chưa
        //Hoặc Kiểm tra xem có cookie và nó trùng với cái code không nếu không thì tạo mới partner tracking
        if ((!Cookie::has('invitePartner')) ||
            (Cookie::has('invitePartner') && $partner->code !== Cookie::get('invitePartner'))) {
            $this->service->storePartnerTracking($partner);
        }
        return $this->sendOkJsonResponse()->withCookie(
            \cookie('invitePartner', $request->get('code'), config('user.invite_partner_timeout') * 24 * 60, null, null, true, true)
        );
    }


    public function clicksChart(DashboardPartnerChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'date');
        $partnerUuid = $request->get('partner_uuid');

        if (!$partnerUuid || $this->partnerService->findOneById($partnerUuid)){
            $clicksChart = $this->service->getPartnerTrackingChartByGroup($startDate, $endDate, $groupBy, $partnerUuid);
            $total = $this->service->getTotalPartnerTrackingChart($startDate, $endDate, $partnerUuid);

            return $this->sendOkJsonResponse([
                'data' => $clicksChart,
                'total' => [
                    'clicks' => $total
                ]
            ]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['partner_uuid' => 'The selected partner uuid is invalid.']]);
    }
}
