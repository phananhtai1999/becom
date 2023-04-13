<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\BillingAddress;
use App\Models\QueryBuilders\BillingAddressQueryBuilder;

class BillingAddressService extends AbstractService
{
    protected $modelClass = BillingAddress::class;

    protected $modelQueryBuilderClass = BillingAddressQueryBuilder::class;
}
