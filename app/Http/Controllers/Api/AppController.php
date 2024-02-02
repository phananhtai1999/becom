<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\PlatformPackageRequest;
use App\Http\Requests\PublishPlatformRequest;
use App\Http\Requests\UpdatePlatformPackageRequest;
use App\Http\Resources\AppResource;
use App\Http\Resources\AppResourceCollection;
use App\Http\Resources\UserAppResource;
use App\Models\App;
use App\Models\UserApp;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\PaypalService;
use App\Services\AppService;
use App\Services\StripeService;
use App\Services\UserAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class AppController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    public function __construct(
        AppService     $service,
        PaypalService  $paypalService,
        StripeService  $stripeService,
        UserAppService $userAppService,
        ConfigService  $configService
    )
    {
        $this->service = $service;
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
        $this->userAppService = $userAppService;
        $this->resourceClass = AppResource::class;
        $this->userPlatformResourceClass = UserAppResource::class;
        $this->resourceCollectionClass = AppResourceCollection::class;
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
        $model->groupApis()->syncWithoutDetaching($request->get('group_api_uuids'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function myPlatformPackage() {
        $myPlatformPackage = $this->userAppService->findOneWhere([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userPlatformResourceClass, $myPlatformPackage)
        );
    }

    public function publishApp($id) {
        $platformPackage = $this->service->findOrFailById($id);
        $paypalProduct = $this->paypalService->createProduct($platformPackage);
        $stripeProduct = $this->stripeService->createProduct($platformPackage->uuid . ' platform package');
        $product = [
            'paypal' => $paypalProduct['id'],
            'stripe' => $stripeProduct['id']
        ];
        $this->service->update($platformPackage,[
            'payment_product_id' => json_encode($product),
            'status' => App::PLATFORM_PACKAGE_PUBLISH
        ]);
        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $platformPackage)
        );
    }
    public function disableApp($id) {
        $platformPackage = $this->service->findOrFailById($id);
        $this->stripeService->disableProduct(json_decode($platformPackage->payment_product_id)->stripe);
        $this->service->update($platformPackage,[
            'status' => App::PLATFORM_PACKAGE_DISABLE
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $platformPackage)
        );
    }

    public function edit(UpdatePlatformPackageRequest $request, $id) {
        $platformPackage = $this->service->findOrFailById($id);
        if ($platformPackage->status == App::PLATFORM_PACKAGE_PUBLISH) {
            return $this->sendJsonResponse(false, 'Can not edit this platform', [], 403);
        }
        $data = $request->all();
        if ($request->get('name')) {
            $data = array_merge($request->all(), ['uuid' => $request->get('name')]);
        }
        $this->service->update($platformPackage, $data);
        $platformPackage->groupApis()->syncWithoutDetaching($request->get('group_api_uuids'));

        Cache::flush();

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $platformPackage)
        );
    }
}
