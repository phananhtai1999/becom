<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PaymentLog;

class PaymentLogsService extends AbstractService
{
    /**
     * @var string
     */
    protected $modelClass = PaymentLog::class;
}
