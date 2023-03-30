<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Services\UnsubscribeService;
use Illuminate\Http\Request;

class UnsubscribeController extends AbstractRestAPIController
{
    public function __construct(UnsubscribeService $service)
    {
        $this->service = $service;
    }

    public function showUnsubscribe($code)
    {
        $model = $this->service->findOrFailById($code);

        if ($model) {
            return $this->sendOkJsonResponse();
        }
    }
}
