<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MapShortcodeRequest;
use App\Http\Requests\ShortCodeGroupRequest;
use App\Http\Requests\UpdateShortCodeGroupRequest;
use App\Http\Resources\ShortCodeGroupResource;
use App\Http\Resources\ShortCodeGroupResourceColleciton;
use App\Services\ShortCodeGroupService;

class ShortCodeGroupController extends Controller
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    public function __construct(ShortCodeGroupService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = ShortCodeGroupResourceColleciton::class;
        $this->resourceClass = ShortCodeGroupResource::class;
        $this->storeRequest = ShortCodeGroupRequest::class;
        $this->editRequest = UpdateShortCodeGroupRequest::class;
        $this->indexRequest = IndexRequest::class;
    }


    public function mappingShortcode(MapShortcodeRequest $request)
    {
        $shortCodeGroup = $this->service->findOrFailById($request->get('short_code_group_uuid'));
        $shortCodeGroup->shortCodes()->syncWithoutDetaching($request->get('short_code_uuids'));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $shortCodeGroup)
        );
    }
}
