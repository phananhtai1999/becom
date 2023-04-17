<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserPaymentByDay;

class UserPaymentByDayService extends AbstractService
{
    protected $modelClass = UserPaymentByDay::class;
}
