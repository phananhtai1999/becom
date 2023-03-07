<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\PlatformPackageRequest;
use App\Http\Resources\PlatformPackageResource;
use App\Http\Resources\PlatformPackageResourceCollection;
use App\Services\ConfigService;
use App\Services\PaypalService;
use App\Services\PlatformPackageService;
use App\Services\StripeService;
use App\Services\UserPlatformPackageService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PlatformPackageController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    public function __construct(
        PlatformPackageService $service,
        PaypalService          $paypalService,
        StripeService          $stripeService,
        UserPlatformPackageService $userPlatformPackageService,
        ConfigService $configService
    )
    {
        $this->service = $service;
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
        $this->userPlatformPackageService = $userPlatformPackageService;
        $this->resourceClass = PlatformPackageResource::class;
        $this->resourceCollectionClass = PlatformPackageResourceCollection::class;
        $this->configService = $configService;
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
            'description' => $request->get('description'),
            'monthly' => $request->get('monthly'),
            'yearly' => $request->get('yearly'),
            'payment_product_id' => json_encode($product)
        ]);
        $model->permissions()->attach($request->get('permission_uuid'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function myPlatformPackage() {
        $myPlatformPackage = $this->userPlatformPackageService->findOneWhereOrFail(['user_uuid' => auth()->user()->getKey()]);

        return $this->sendOkJsonResponse(['data' => $myPlatformPackage]);
    }
}
