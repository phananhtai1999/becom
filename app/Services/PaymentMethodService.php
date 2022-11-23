<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PaymentMethod;
use App\Models\QueryBuilders\PaymentMethodQueryBuilder;

class PaymentMethodService extends AbstractService
{
    protected $modelClass = PaymentMethod::class;

    protected $modelQueryBuilderClass = PaymentMethodQueryBuilder::class;
}
