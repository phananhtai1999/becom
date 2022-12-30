<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\MailSendingHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyMailSendingHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return MailSendingHistory::select('mail_sending_history.*')
            ->join('campaigns', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
            ->where('campaigns.user_uuid', auth()->user()->getkey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new MailSendingHistory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'campaign_uuid',
                'email',
                'time',
                'status',
                'campaign_scenario_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'campaign_uuid',
                'email',
                'time',
                'status',
                'campaign_scenario_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'campaign_uuid',
                AllowedFilter::exact('exact__campaign_uuid', 'campaign_uuid'),
                'campaign_scenario_uuid',
                AllowedFilter::exact('exact__campaign_scenario_uuid', 'campaign_scenario_uuid'),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'time',
                AllowedFilter::exact('exact__time', 'time'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'campaign.tracking_key',
                AllowedFilter::exact('exact__campaign.tracking_key', 'campaign.tracking_key'),
                'campaign.send_type',
                AllowedFilter::exact('exact__campaign.send_type', 'campaign.send_type'),
                'campaign.mailTemplate.subject',
                AllowedFilter::exact('exact__campaign.mailTemplate.subject', 'campaign.mailTemplate.subject'),
                'campaign.smtpAccount.mail_from_name',
                AllowedFilter::exact('exact__campaign.smtpAccount.mail_from_name', 'campaign.smtpAccount.mail_from_name'),
                'campaign.website.domain',
                AllowedFilter::exact('exact__campaign.website.domain', 'campaign.website.domain'),
            ]);
    }
}
