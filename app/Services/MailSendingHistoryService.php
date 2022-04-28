<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;

class MailSendingHistoryService extends AbstractService
{
    protected $modelClass = MailSendingHistory::class;

    /**
     * @param $perPage
     * @return mixed
     */
    public function indexMyMailSendingHistory($perPage)
    {
        return $this->model->select('mail_sending_history.*')
            ->join('campaigns', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->paginate($perPage);
    }
}
