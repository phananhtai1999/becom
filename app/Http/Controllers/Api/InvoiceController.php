<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Abstracts\AbstractService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;

class InvoiceController extends AbstractRestAPIController
{
    use RestShowTrait;

    public function __construct(
        InvoiceService   $service,
    )
    {
        $this->service = $service;
        $this->resourceClass = InvoiceResource::class;
    }
}
