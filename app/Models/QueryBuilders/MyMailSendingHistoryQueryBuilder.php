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
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey());
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
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'campaign_uuid',
                'email',
                'time',
                'status',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'campaign_uuid',
                AllowedFilter::exact('exact__campaign_uuid', 'campaign_uuid'),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'time',
                AllowedFilter::exact('exact__time', 'time'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'campaign.tracking_key',
                AllowedFilter::exact('exact__campaign.tracking_key', 'campaign.tracking_key'),
            ]);
    }
}
