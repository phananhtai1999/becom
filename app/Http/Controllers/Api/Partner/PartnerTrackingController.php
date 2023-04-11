<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerTrackingRequest;
use App\Http\Requests\UpdatePartnerTrackingRequest;
use App\Http\Resources\PartnerTrackingResource;
use App\Http\Resources\PartnerTrackingResourceCollection;
use App\Services\PartnerService;
use App\Services\PartnerTrackingService;
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
        if (!Cookie::has('invitePartner')) {
            try {
                $iP = geoip()->getClientIP();
                $this->service->create([
                    'partner_uuid' => $partner->uuid,
                    'ip' => $iP,
                    'country' => geoip()->getLocation($iP)->country
                ]);
            }catch (\Exception $e) {
                $this->service->create([
                    'partner_uuid' => $partner->uuid,
                    'ip' => $request->ip()
                ]);
            }
        }
        //Kiểm tra xem có cookie và nó trùng với cái code không nếu không thì update lại partner_tracking
        if (Cookie::has('invitePartner') && $partner->code !== Cookie::get('invitePartner')) {
            $partnerTracking = $this->service->findOneWhere(['ip' => geoip()->getClientIP()]);
            if ($partnerTracking) {
                $this->service->update($partnerTracking, [
                    'partner_uuid' => $partner->uuid
                ]);
            }
        }
        return $this->sendOkJsonResponse()->withCookie(
            \cookie('invitePartner', $request->get('code'), config('user.invite_partner_timeout') * 24 * 60, null, null, false, true)
        );
    }
}
