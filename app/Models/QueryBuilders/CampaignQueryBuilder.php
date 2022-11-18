<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Campaign::class;
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
                'type',
                'smtp_account_uuid',
                'website_uuid',
                'user_uuid',
                'was_finished',
                'was_stopped_by_owner',
                'send_type',
                'open_within',
                'open_mail_campaign',
                'not_open_mail_campaign',
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
                'type',
                'smtp_account_uuid',
                'website_uuid',
                'user_uuid',
                'was_finished',
                'was_stopped_by_owner',
                'send_type',
                'open_within',
                'open_mail_campaign',
                'not_open_mail_campaign',
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
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'send_type',
                AllowedFilter::exact('exact__send_type', 'send_type'),
                'smtp_account_uuid',
                AllowedFilter::exact('exact__smtp_account_uuid', 'smtp_account_uuid'),
                'website_uuid',
                AllowedFilter::exact('exact__website_uuid', 'website_uuid'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'was_finished',
                AllowedFilter::exact('exact__was_finished', 'was_finished'),
                'was_stopped_by_owner',
                AllowedFilter::exact('exact__was_stopped_by_owner', 'was_stopped_by_owner'),
                'open_within',
                AllowedFilter::exact('exact__open_within', 'open_within'),
                'open_mail_campaign',
                AllowedFilter::exact('exact__open_mail_campaign', 'open_mail_campaign'),
                'not_open_mail_campaign',
                AllowedFilter::exact('exact__not_open_mail_campaign', 'not_open_mail_campaign'),
                'mailTemplate.subject',
                AllowedFilter::exact('exact__mailTemplate.subject', 'mailTemplate.subject'),
                'smtpAccount.mail_username',
                AllowedFilter::exact('exact__smtpAccount.mail_username', 'smtpAccount.mail_username'),
                'website.domain',
                AllowedFilter::exact('exact__website.domain', 'website.domain'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
            ]);
    }
}
