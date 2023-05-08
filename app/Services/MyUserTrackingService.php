<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\SendNotificationSystemEvent;
use App\Models\Notification;
use App\Models\QueryBuilders\MyUserTrackingQueryBuilder;
use App\Models\QueryBuilders\UserTrackingQueryBuilder;
use App\Models\UserTracking;

class MyUserTrackingService extends AbstractService
{
    protected $modelClass = UserTracking::class;

    protected $modelQueryBuilderClass = MyUserTrackingQueryBuilder::class;
}
