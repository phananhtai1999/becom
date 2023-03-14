<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ConfigRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateCachePlatformConfig;
use App\Http\Requests\UpdateConfigRequest;
use App\Http\Requests\UpsertConfigRequest;
use App\Http\Resources\ConfigResourceCollection;
use App\Http\Resources\ConfigResource;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Services\ConfigService;
use Illuminate\Http\JsonResponse;

class ConfigController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait;

    /**
     * @param ConfigService $service
     */
    public function __construct(ConfigService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = ConfigResourceCollection::class;
        $this->resourceClass = ConfigResource::class;
        $this->storeRequest = ConfigRequest::class;
        $this->editRequest = UpdateConfigRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        // Cast value to integer
        if ($request->type === 'boolean' || $request->type === 'number') {
            if ($request->value == 0) {
                $value = '00';
                $castValue = (float)$value;
            } else {
                $castValue = (float)$request->value;
            }
        } else {
            $castValue = $request->value;
        }

        $model = $this->service->create(array_merge($request->all(), [
            'value' => $castValue
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        // Cast value to integer
        if ($request->type === 'boolean' || $request->type === 'number') {
            if ($request->value == 0) {
                $value = '00';
                $castValue = (float)$value;
            } else {
                $castValue = (float)$request->value;
            }
        } else {
            $castValue = $request->value;
        }

        $this->service->update($model, array_merge($request->all(), [
            'value' => $castValue
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function loadAllConfig(): JsonResponse
    {
        $models = $this->service->loadAllConfig();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param UpsertConfigRequest $request
     * @return JsonResponse
     */
    public function upsertConfig(UpsertConfigRequest $request)
    {
        $model = $this->service->findConfigByKey($request->get('key'));

        if (empty($model)) {
            $request = app($this->storeRequest);

            $model = $this->service->create($request->all());
        } else {

            $this->service->update($model, $request->all());
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function getCachePlatformConfig($id)
    {
        $cache = $this->service->findOneWhere(['key' => $id . '_cache']);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $cache)
        );
    }

    public function editCachePlatformConfig($platformPackageUuid, UpdateCachePlatformConfig $request)
    {
        $model = $this->service->findOneWhere(['key' => $platformPackageUuid . '_cache']);
        $this->service->update($model, $request->all());

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
