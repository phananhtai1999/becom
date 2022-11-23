<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Order;
use App\Models\QueryBuilders\OrderQueryBuilder;

class OrderService extends AbstractService
{
    protected $modelClass = Order::class;

    protected $modelQueryBuilderClass = OrderQueryBuilder::class;
}
