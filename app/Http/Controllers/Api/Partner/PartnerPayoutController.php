<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\ConfirmWithdrawalRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyPartnerPayoutRequest;
use App\Http\Requests\PartnerCategoryRequest;
use App\Http\Requests\UpdatePartnerCategoryRequest;
use App\Http\Requests\UpdateWebsitePageCategoryRequest;
use App\Http\Requests\WebsitePageCategoryRequest;
use App\Http\Resources\PartnerCategoryResource;
use App\Http\Resources\PartnerCategoryResourceCollection;
use App\Http\Resources\PartnerPayoutResource;
use App\Http\Resources\PartnerPayoutResourceCollection;
use Techup\ApiConfig\Services\LanguageService;
use App\Services\MyPartnerPayoutService;
use App\Services\PartnerCategoryService;
use App\Services\PartnerPayoutService;
use App\Services\PartnerService;
use App\Services\PartnerTrackingByYearService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class PartnerPayoutController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MyPartnerPayoutService
     */
    protected $myService;

    protected $partnerService;

    protected $partnerTrackingByYearService;

    public function __construct(
        PartnerPayoutService $service,
        MyPartnerPayoutService $myService,
        PartnerService $partnerService,
        PartnerTrackingByYearService $partnerTrackingByYearService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerPayoutResourceCollection::class;
        $this->resourceClass = PartnerPayoutResource::class;
        $this->storeRequest = PartnerCategoryRequest::class;
        $this->editRequest = UpdatePartnerCategoryRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->partnerService = $partnerService;
        $this->partnerTrackingByYearService = $partnerTrackingByYearService;
        $this->myService = $myService;
    }

    public function withdrawal(MyPartnerPayoutRequest $request)
    {
        $amountWantWithdrawn = $request->get('amount');
        $partner = $this->partnerService->findOneWhereOrFail([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);
        $amountCanWithdrawn = $partner->unpaid_earnings;

        if ($amountWantWithdrawn > $amountCanWithdrawn) {
            return $this->sendValidationFailedJsonResponse(['errors' => 'Your balance is not enough']);
        }
        $this->service->create([
            'partner_uuid' => $partner->uuid,
            'amount'=> $amountWantWithdrawn,
            'payout_method_uuid'=> $request->get('payout_method_uuid'),
        ]);
        return $this->sendOkJsonResponse();
    }

    public function confirmWithdrawal(ConfirmWithdrawalRequest $request)
    {
        $status = $request->get('status');
        $partnerPayoutUuids = $request->get('partner_payouts');
        foreach ($partnerPayoutUuids as $partnerPayoutUuid) {
            $partnerPayout = $this->service->findOneById($partnerPayoutUuid);
            $this->service->update($partnerPayout, [
                'status' => $status,
                'time' => Carbon::now(),
                'by_user_uuid' => auth()->userId(),
                'app_id' => auth()->appId(),
            ]);
        }

        return $this->sendOkJsonResponse();
    }
}
