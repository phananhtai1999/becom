<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyCampaignQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Campaign::select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Campaign())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'tracking_key',
                'mail_template_uuid',
                'from_date',
                'to_date',
                'number_email_per_date',
                'number_email_per_user',
                'status',
                'smtp_account_uuid',
                'website_uuid',
                'is_running',
                'was_finished',
                'was_stopped_by_owner'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'tracking_key',
                'mail_template_uuid',
                'from_date',
                'to_date',
                'number_email_per_date',
                'number_email_per_user',
                'status',
                'smtp_account_uuid',
                'website_uuid',
                'is_running',
                'was_finished',
                'was_stopped_by_owner'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'tracking_key',
                AllowedFilter::exact('exact__tracking_key', 'tracking_key'),
                'mail_template_uuid',
                AllowedFilter::exact('exact__mail_template_uuid', 'mail_template_uuid'),
                'from_date',
                AllowedFilter::exact('exact__from_date', 'from_date'),
                'to_date',
                AllowedFilter::exact('exact__to_date', 'to_date'),
                'number_email_per_date',
                AllowedFilter::exact('exact__number_email_per_date', 'number_email_per_date'),
                'number_email_per_user',
                AllowedFilter::exact('exact__number_email_per_user', 'number_email_per_user'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'smtp_account_uuid',
                AllowedFilter::exact('exact__smtp_account_uuid', 'smtp_account_uuid'),
                'website_uuid',
                AllowedFilter::exact('exact__website_uuid', 'website_uuid'),
                'is_running',
                AllowedFilter::exact('exact__is_running', 'is_running'),
                'was_finished',
                AllowedFilter::exact('exact__was_finished', 'was_finished'),
                'was_stopped_by_owner',
                AllowedFilter::exact('exact__was_stopped_by_owner', 'was_stopped_by_owner'),
            ]);
    }
}
