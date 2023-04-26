<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\MailSendingHistory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Sorts\SortOpenTracking;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
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
            ->defaultSort('-uuid')
            ->allowedSorts([
                $modelKeyName,
                'campaign_uuid',
                'email',
                'time',
                'status',
                'campaign_scenario_uuid',
                AllowedSort::custom('view', new SortOpenTracking())
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
                'campaign.sendProject.domain',
                AllowedFilter::exact('exact__campaign.sendProject.domain', 'campaign.sendProject.domain'),
                'campaign.sendProject.name',
                AllowedFilter::exact('exact__campaign.sendProject.name', 'campaign.sendProject.name'),
                'campaign.user.username',
                AllowedFilter::exact('exact__campaign.user.username', 'campaign.user.username'),
                'campaign.user.email',
                AllowedFilter::exact('exact__campaign.user.email', 'campaign.user.email'),
                AllowedFilter::scope('from__time'),
                AllowedFilter::scope('to__time'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return MailSendingHistory::class;
    }

    /**
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public static function searchQuery($search, $searchBy)
    {
        $initialQuery = static::initialQuery();
        $baseQuery = static::fillAble();

        return SearchQueryBuilder::search($baseQuery, $initialQuery, $search, $searchBy);
    }
}
