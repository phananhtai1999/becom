<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\SendEmailScheduleLog;

class SendEmailScheduleLogService extends AbstractService
{
    protected $modelClass = SendEmailScheduleLog::class;

}
