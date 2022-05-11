<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SmtpAccount;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class SmtpAccountQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return SmtpAccount::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new SmtpAccount())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'mail_mailer',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'smtp_mail_encryption_uuid',
                'mail_from_address',
                'mail_from_name',
                'secret_key',
                'website_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'mail_mailer',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'smtp_mail_encryption_uuid',
                'mail_from_address',
                'mail_from_name',
                'secret_key',
                'website_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'mail_mailer',
                AllowedFilter::exact('exact__mail_mailer', 'mail_mailer'),
                'mail_host',
                AllowedFilter::exact('exact__mail_host', 'mail_host'),
                'mail_port',
                AllowedFilter::exact('exact__mail_port', 'mail_port'),
                'mail_username',
                AllowedFilter::exact('exact__mail_username', 'mail_username'),
                'mail_password',
                AllowedFilter::exact('exact__mail_password', 'mail_password'),
                'smtp_mail_encryption_uuid',
                AllowedFilter::exact('exact__smtp_mail_encryption_uuid', 'smtp_mail_encryption_uuid'),
                'mail_from_address',
                AllowedFilter::exact('exact__mail_from_address', 'mail_from_address'),
                'mail_from_name',
                AllowedFilter::exact('exact__mail_from_name', 'mail_from_name'),
                'secret_key',
                AllowedFilter::exact('exact__secret_key', 'secret_key'),
                'website_uuid',
                AllowedFilter::exact('exact__website_uuid', 'website_uuid'),
            ]);
    }
}