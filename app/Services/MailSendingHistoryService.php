<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;
use App\Models\QueryBuilders\MailSendingHistoryQueryBuilder;

class MailSendingHistoryService extends AbstractService
{
    protected $modelClass = MailSendingHistory::class;

    protected $modelQueryBuilderClass = MailSendingHistoryQueryBuilder::class;
}
