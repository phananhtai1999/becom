<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\WebsiteVerificationResource;
use App\Http\Resources\WebsiteVerificationResourceCollection;
use App\Services\WebsiteVerificationService;

class WebsiteVerificationController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @param WebsiteVerificationService $service
     */
    public function __construct(WebsiteVerificationService $service)
    {
        $this->service = $service;
        $this->resourceClass = WebsiteVerificationResource::class;
        $this->resourceCollectionClass = WebsiteVerificationResourceCollection::class;
    }
}
