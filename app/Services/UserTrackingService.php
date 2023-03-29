<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\SendNotificationSystemEvent;
use App\Models\Notification;
use App\Models\QueryBuilders\UserTrackingQueryBuilder;
use App\Models\UserTracking;

class UserTrackingService extends AbstractService
{
    protected $modelClass = UserTracking::class;

    protected $modelQueryBuilderClass = UserTrackingQueryBuilder::class;
}
