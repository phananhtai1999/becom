<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Http\Resources\SubscriptionPlanResource;
use App\Http\Resources\SubscriptionPlanResourceCollection;
use App\Services\PaypalService;
use App\Services\AppService;
use App\Services\StripeService;
use App\Services\SubscriptionPlanService;

class SubscriptionPlanController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;
    public function __construct(
        SubscriptionPlanService $subscriptionPlanService,
        PaypalService           $paypalService,
        StripeService           $stripeService,
        AppService $platformPackageService
    )
    {
        $this->service = $subscriptionPlanService;
        $this->paypalService = $paypalService;
        $this->stripeService = $stripeService;
        $this->platformPacakageService = $platformPackageService;
        $this->resourceClass = SubscriptionPlanResource::class;
        $this->resourceCollectionClass = SubscriptionPlanResourceCollection::class;
    }

    public function store(SubscriptionPlanRequest $request)
    {
        $isExist = $this->service->checkExist($request);
        if ($isExist) {
            return $this->sendJsonResponse(false, 'This plan for this platform package already exists', [], 400);
        }
        $platformPackage = $this->platformPacakageService->findOrFailById($request->get('app_uuid'));
        $product = json_decode($platformPackage->payment_product_id);
        if ($request->get('duration_type') == "month") {
            $price = $platformPackage->monthly;
        } else {
            $price = $platformPackage->yearly;
        }
        $paypalPlan = $this->paypalService->createPlan($product->paypal, $request, $price, $request->get('app_uuid') . ' platform package');
        $stripePlan = $this->stripeService->createPlan($product->stripe, $request, $price);
        $product = [
            'paypal' => $paypalPlan['plan_id'],
            'stripe' => $stripePlan['plan_id']
        ];

        $model = $this->service->create(array_merge($request->all(), [
            'payment_plan_id' => $product,
            'duration' => $request->get('duration', 1)
        ]));
        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
