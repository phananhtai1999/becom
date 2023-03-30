<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AddOnSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Http\Resources\AddOnSubscriptionPlanResource;
use App\Http\Resources\AddOnSubscriptionPlanResourceCollection;
use App\Http\Resources\SubscriptionPlanResource;
use App\Http\Resources\SubscriptionPlanResourceCollection;
use App\Models\AddOnSubscriptionPlan;
use App\Services\AddOnService;
use App\Services\AddOnSubscriptionPlanService;
use App\Services\PaypalService;
use App\Services\PlatformPackageService;
use App\Services\StripeService;
use App\Services\SubscriptionPlanService;

class AddOnSubscriptionPlanController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;
    public function __construct(
        AddOnSubscriptionPlanService $subscriptionPlanService,
        PaypalService           $paypalService,
        StripeService           $stripeService,
        AddOnService  $addOnService
    )
    {
        $this->service = $subscriptionPlanService;
        $this->paypalService = $paypalService;
        $this->stripeService = $stripeService;
        $this->addOnService = $addOnService;
        $this->resourceClass = AddOnSubscriptionPlanResource::class;
        $this->resourceCollectionClass = AddOnSubscriptionPlanResourceCollection::class;
    }

    public function store(AddOnSubscriptionPlanRequest $request)
    {
        $isExist = $this->service->checkExist($request);
        if ($isExist) {
            return $this->sendJsonResponse(false, 'This plan for this add-on already exists', [], 400);
        }
        $addOn = $this->addOnService->findOrFailById($request->get('add_on_uuid'));
        if ($request->get('duration_type') == "month") {
            $price = $addOn->monthly;
        } else {
            $price = $addOn->yearly;
        }
        $paypalPlan = $this->paypalService->createPlan($addOn->payment_product_id['paypal'], $request, $price);
        $stripePlan = $this->stripeService->createPlan($addOn->payment_product_id['stripe'], $request, $price);
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
