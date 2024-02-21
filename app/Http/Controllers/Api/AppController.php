<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PlatformPackageRequest;
use App\Http\Requests\PublishPlatformRequest;
use App\Http\Requests\UpdatePlatformPackageRequest;
use App\Http\Resources\AppResource;
use App\Http\Resources\AppResourceCollection;
use App\Http\Resources\UserAppResource;
use App\Models\App;
use App\Models\UserApp;
use Illuminate\Support\Str;
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
        $uuid = strtolower(str_replace(' ', '_', Str::snake($request->get('name'))));
        $model = $this->service->create(array_merge($request->all(), ['uuid' => $uuid]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function myPlatformPackage(IndexRequest $request) {
        $myApps = $this->service->myOwnerApps($request, auth()->userId());

        return $this->sendCreatedJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $myApps)
        );
    }

    /**
     * @return JsonResponse
     */
    public function userApps() {

        $myApps = $this->service->myApps(auth()->userId());
        return $this->sendCreatedJsonResponse(['apps' => $myApps ]
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
        $this->service->update($platformPackage, $request->all());


        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $platformPackage)
        );
    }


     /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {

        $platformPackage = $this->service->findOrFailById($id);
        if ($platformPackage->status == App::PLATFORM_PACKAGE_PUBLISH) {
            return $this->sendJsonResponse(false, 'Can not delete this platform', [], 403);
        }

        $this->service->destroy($id);

        return $this->sendOkJsonResponse();
    }

    public function getAppOfDepartment(IndexRequest $request, $id): JsonResponse
    {

        $apps = $this->service->getAppByDepartment($request, $id);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceCollectionClass, $apps)
        );
    }
}
