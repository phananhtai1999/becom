<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\UserProfile;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class UserProfileQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return UserProfile::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new UserProfile())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'email_verified_at',
                'email_verification_code',
                'banned_at',
                'avatar_img',
                'cover_img',
                'credit',
                'can_add_smtp_account',
                'can_remove_footer_template'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'email_verified_at',
                'email_verification_code',
                'banned_at',
                'avatar_img',
                'cover_img',
                'credit',
                'can_add_smtp_account',
                'can_remove_footer_template'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
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
                'roles.name',
                AllowedFilter::exact('exact__roles.name', 'roles.name'),
                'credit',
                AllowedFilter::exact('exact__credit', 'credit'),
                'can_add_smtp_account',
                AllowedFilter::exact('exact__can_add_smtp_account', 'can_add_smtp_account'),
                'can_remove_footer_template',
                AllowedFilter::exact('exact__can_remove_footer_template', 'can_remove_footer_template'),
                AllowedFilter::scope('from__banned_at'),
                AllowedFilter::scope('to__banned_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return UserProfile::class;
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
