<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\PlatformPackageRequest;
use App\Http\Resources\PlatformPackageResource;
use App\Services\PaypalService;
use App\Services\PlatformPackageService;
use App\Services\StripeService;

class PlatformPackageController extends AbstractRestAPIController
{
    public function __construct(
        PlatformPackageService $service,
        PaypalService          $paypalService,
        StripeService          $stripeService
    )
    {
        $this->service = $service;
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
        $this->resourceClass = PlatformPackageResource::class;
    }

    public function store(PlatformPackageRequest $request)
    {
        $paypalProduct = $this->paypalService->createProduct($request);
        $stripeProduct = $this->stripeService->createProduct($request);
        $product = [
            'paypal' => $paypalProduct['id'],
            'stripe' => $stripeProduct['id']
        ];
        $model = $this->service->create([
            'uuid' => $request->get('name'),
            'monthly' => $request->get('monthly'),
            'yearly' => $request->get('yearly'),
            'payment_product_id' => json_encode($product)
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
