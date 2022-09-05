<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailOpenTracking;

class MailOpenTrackingService extends AbstractService
{
    protected $modelClass = MailOpenTracking::class;

    /**
     * @param $mailSendingHistoryUuid
     * @param $ip
     * @param $userAgent
     * @return mixed
     */
    public function mailOpenTracking($mailSendingHistoryUuid, $ip, $userAgent)
    {
        return $this->create([
            'mail_sending_history_uuid' => $mailSendingHistoryUuid,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);
    }
}
