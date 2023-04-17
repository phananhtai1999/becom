<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
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
        return Campaign::where('user_uuid', auth()->user()->getKey());
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
                'status',
                'type',
                'smtp_account_uuid',
                'send_project_uuid',
                'user_uuid',
                'reply_to_email',
                'reply_name',
                'was_finished',
                'was_stopped_by_owner',
                'send_type',
                'send_from_name',
                'send_from_email',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'tracking_key',
                'mail_template_uuid',
                'from_date',
                'to_date',
                'status',
                'type',
                'smtp_account_uuid',
                'send_project_uuid',
                'user_uuid',
                'reply_to_email',
                'reply_name',
                'was_finished',
                'was_stopped_by_owner',
                'send_type',
                'send_from_name',
                'send_from_email',
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
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'send_type',
                AllowedFilter::exact('exact__send_type', 'send_type'),
                'smtp_account_uuid',
                AllowedFilter::exact('exact__smtp_account_uuid', 'smtp_account_uuid'),
                'send_project_uuid',
                AllowedFilter::exact('exact__send_project_uuid', 'send_project_uuid'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'reply_to_email',
                AllowedFilter::exact('exact__reply_to_email', 'reply_to_email'),
                'reply_name',
                AllowedFilter::exact('exact__reply_name', 'reply_name'),
                'send_from_name',
                AllowedFilter::exact('exact__send_from_name', 'send_from_name'),
                'send_from_email',
                AllowedFilter::exact('exact__send_from_email', 'send_from_email'),
                'was_finished',
                AllowedFilter::exact('exact__was_finished', 'was_finished'),
                'was_stopped_by_owner',
                AllowedFilter::exact('exact__was_stopped_by_owner', 'was_stopped_by_owner'),
                'mailTemplate.subject',
                AllowedFilter::exact('exact__mailTemplate.subject', 'mailTemplate.subject'),
                'smtpAccount.mail_username',
                AllowedFilter::exact('exact__smtpAccount.mail_username', 'smtpAccount.mail_username'),
                'smtpAccount.mail_mailer',
                AllowedFilter::exact('exact__smtpAccount.mail_mailer', 'smtpAccount.mail_mailer'),
                'sendProject.domain',
                AllowedFilter::exact('exact__sendProject.domain', 'sendProject.domain'),
                'sendProject.name',
                AllowedFilter::exact('exact__sendProject.name', 'sendProject.name'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'contactLists.uuid',
                AllowedFilter::exact('exact__contactLists.uuid', 'contactLists.uuid'),
                AllowedFilter::scope('from__from_date'),
                AllowedFilter::scope('to__from_date'),
                AllowedFilter::scope('from__to_date'),
                AllowedFilter::scope('to__to_date'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Campaign::class;
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
