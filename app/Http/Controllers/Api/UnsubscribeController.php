<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\UnsubscribeResource;
use App\Services\UnsubscribeService;
use Illuminate\Http\Request;

class UnsubscribeController extends AbstractRestAPIController
{
    use RestShowTrait;
    public function __construct(UnsubscribeService $service)
    {
        $this->service = $service;
        $this->resourceClass = UnsubscribeResource::class;
    }
}
