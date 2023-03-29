<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Notification;
use App\Models\QueryBuilders\NotificationQueryBuilder;

class NotificationService extends AbstractService
{
    protected $modelClass = Notification::class;

    protected $modelQueryBuilderClass = NotificationQueryBuilder::class;
}
