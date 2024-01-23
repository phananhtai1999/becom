<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\PlatformPackageRequest;
use App\Http\Requests\PublishPlatformRequest;
use App\Http\Requests\UpdatePlatformPackageRequest;
use App\Http\Resources\PlatformPackageResource;
use App\Http\Resources\PlatformPackageResourceCollection;
use App\Http\Resources\UserPlatformPackageResource;
use App\Models\PlatformPackage;
use App\Models\UserPlatformPackage;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\PaypalService;
use App\Services\PlatformPackageService;
use App\Services\StripeService;
use App\Services\UserPlatformPackageService;
use Illuminate\Http\JsonResponse;
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
        $this->userPlatformResourceClass = UserPlatformPackageResource::class;
        $this->resourceCollectionClass = PlatformPackageResourceCollection::class;
        $this->configService = $configService;
    }

    /**
     * @param PlatformPackageRequest $request
     * @return JsonResponse
     */
    public function store(PlatformPackageRequest $request)
    {
        $model = $this->service->create([
            'uuid' => $request->get('name'),
            'description' => $request->get('description'),
            'monthly' => $request->get('monthly'),
            'yearly' => $request->get('yearly')
        ]);
        $model->permissions()->attach($request->get('permission_uuid'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function myPlatformPackage() {
        $myPlatformPackage = $this->userPlatformPackageService->findOneWhere([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userPlatformResourceClass, $myPlatformPackage)
        );
    }

    public function publishPlatformPackage($id) {
        $platformPackage = $this->service->findOrFailById($id);
        $paypalProduct = $this->paypalService->createProduct($platformPackage);
        $stripeProduct = $this->stripeService->createProduct($platformPackage->uuid . ' platform package');
        $product = [
            'paypal' => $paypalProduct['id'],
            'stripe' => $stripeProduct['id']
        ];
        $this->service->update($platformPackage,[
            'payment_product_id' => json_encode($product),
            'status' => PlatformPackage::PLATFORM_PACKAGE_PUBLISH
        ]);
        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $platformPackage)
        );
    }
    public function disablePlatformPackage($id) {
        $platformPackage = $this->service->findOrFailById($id);
        $this->stripeService->disableProduct(json_decode($platformPackage->payment_product_id)->stripe);
        $this->service->update($platformPackage,[
            'status' => PlatformPackage::PLATFORM_PACKAGE_DISABLE
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $platformPackage)
        );
    }

    public function edit(UpdatePlatformPackageRequest $request, $id) {
        $platformPackage = $this->service->findOrFailById($id);
        if ($platformPackage->status == PlatformPackage::PLATFORM_PACKAGE_PUBLISH) {
            return $this->sendJsonResponse(false, 'Can not edit this platform', [], 403);
        }
        $this->service->update($platformPackage, array_merge($request->all(), ['uuid' => $request->get('name')]));
        Cache::flush();

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $platformPackage)
        );
    }
}
