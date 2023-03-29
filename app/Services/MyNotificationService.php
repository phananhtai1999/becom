<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Notification;
use App\Models\QueryBuilders\MyNotificationQueryBuilder;

class MyNotificationService extends AbstractService
{
    protected $modelClass = Notification::class;

    protected $modelQueryBuilderClass = MyNotificationQueryBuilder::class;
}
