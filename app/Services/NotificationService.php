<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Notification;
use App\Models\QueryBuilders\NotificationQueryBuilder;

class NotificationService extends AbstractService
{
    protected $modelClass = Notification::class;

    protected $modelQueryBuilderClass = NotificationQueryBuilder::class;

    public function statisticalNotification($where = null)
    {
        return $this->model->selectRaw("COUNT(uuid) as `total`, COUNT(IF( `read` = 1, 1, NULL ) ) as `total_read`, COUNT(IF(`read` = 0, 1, NULL ) ) as `total_not_read`")
            ->where($where)->first();
    }
}
