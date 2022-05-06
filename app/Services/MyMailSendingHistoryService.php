<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;
use App\Models\QueryBuilders\MailSendingHistoryQueryBuilder;
use App\Models\QueryBuilders\MyMailSendingHistoryQueryBuilder;

class MyMailSendingHistoryService extends AbstractService
{
    protected $modelClass = MailSendingHistory::class;

    protected $modelQueryBuilderClass = MyMailSendingHistoryQueryBuilder::class;

    /**
     * @param $id
     * @return void
     */
    public function findMyMailSendingHistoryByKeyOrAbort($id)
    {
        $mailSendingHistory = $this->model->select('mail_sending_history.*')
            ->join('campaigns', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['mail_sending_history.uuid', $id]
            ])->first();

        if (!empty($mailSendingHistory)) {
            return $mailSendingHistory;
        } else {
            abort(403, 'Unauthorized');
        }
    }
}
