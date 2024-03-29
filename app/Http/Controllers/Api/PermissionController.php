<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\CreditPackageRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\UpdateCreditPackageRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\CreditPackageResource;
use App\Http\Resources\CreditPackageResourceCollection;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\PermissionResourceCollection;
use App\Models\Permission;
use App\Services\AddOnService;
use App\Services\CreditPackageService;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Cache;

class PermissionController extends AbstractRestAPIController
{

    use RestStoreTrait, RestDestroyTrait, RestEditTrait, RestShowTrait, RestIndexTrait;

    public function __construct(PermissionService $service, AddOnService $addOnService)
    {
        $this->service = $service;
        $this->addOnService = $addOnService;
        $this->resourceCollectionClass = PermissionResourceCollection::class;
        $this->resourceClass = PermissionResource::class;
        $this->storeRequest = PermissionRequest::class;
        $this->editRequest = UpdatePermissionRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    public function permissionForPlatform() {
        $addOns = $this->addOnService->findAllWhere([]);
        $permissionAddOnUuid = [];
        foreach ($addOns as $addOn) {
            foreach ($addOn->permissions as $permission) {
                $permissionAddOnUuid[] = $permission->uuid;
            }
        }
        $models = $this->service->getPermissionForPlatform(array_unique($permissionAddOnUuid));
        Cache::flush();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
