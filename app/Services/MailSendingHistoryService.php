<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;

class MailSendingHistoryService extends AbstractService
{
    protected $modelClass = MailSendingHistory::class;
}
