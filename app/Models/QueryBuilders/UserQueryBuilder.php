<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class UserQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return User::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new User())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'username',
                'first_name',
                'last_name',
                'email',
                'email_verified_at',
                'email_verification_code',
                'banned_at',
                'avatar_img',
                'cover_img',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'username',
                'first_name',
                'last_name',
                'email',
                'email_verified_at',
                'email_verification_code',
                'banned_at',
                'avatar_img',
                'cover_img',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'username',
                AllowedFilter::exact('exact__username', 'username'),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'email_verified_at',
                AllowedFilter::exact('exact__email_verified_at', 'email_verified_at'),
                'email_verification_code',
                AllowedFilter::exact('exact__email_verification_code', 'email_verification_code'),
                'banned_at',
                AllowedFilter::exact('exact__banned_at', 'banned_at'),
                'avatar_img',
                AllowedFilter::exact('exact__avatar_img', 'avatar_img'),
                'cover_img',
                AllowedFilter::exact('exact__cover_img', 'cover_img'),
            ]);
    }
}