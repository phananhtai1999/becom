<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResourceCollection;
use Techup\ApiConfig\Models\Config;
use App\Models\Role;
use App\Services\RoleService;

class RoleController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    /**
     * @param RoleService $service
     */
    public function __construct(RoleService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = RoleResourceCollection::class;
        $this->resourceClass = RoleResource::class;
        $this->storeRequest = RoleRequest::class;
        $this->editRequest = UpdateRoleRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAdmin(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['name', '<>', Role::ROLE_ROOT]
        ]);

        return $this->sendOkJsonResponse($this->service->resourceCollectionToData(
            $this->resourceCollectionClass, $models
        ));
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicRoles(IndexRequest $request)
    {
        $models = $this->service->getPluckModel($request, ['uuid', 'name', 'slug']);

        return $this->sendOkJsonResponse($this->service->resourceCollectionToData(
            $this->resourceCollectionClass, $models
        ));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAdmin($id)
    {
        $model = $this->service->showRoleOfAdminById($id);

        return $this->sendOkJsonResponse($this->service->resourceToData(
            $this->resourceClass, $model
        ));
    }
}
