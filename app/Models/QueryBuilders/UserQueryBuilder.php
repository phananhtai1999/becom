<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
                'credit',
                'can_add_smtp_account',
                'can_remove_footer_template'
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
                'credit',
                'can_add_smtp_account',
                'can_remove_footer_template'
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
                //Custom filter full_name Append (LIKE)
                AllowedFilter::callback("full_name", function (Builder $query, $values) {
                    if (is_array($values)) {
                        $query->where(function ($q) use ($values) {
                            foreach ($values as $value) {
                                $fullName = ltrim($value, ' ');
                                $q->orWhereRaw("CONCAT(first_name, ' ', last_name) like '%$fullName%'");
                            }
                        });
                    } else {
                        $query->whereRaw("CONCAT(first_name, ' ', last_name) like '%$values%'");
                    }
                }),
                //Custom filter full_name Append (EXACT)
                AllowedFilter::callback("exact__full_name", function (Builder $query, $values) {
                    if (is_array($values)) {
                        $query->where(function ($q) use ($values) {
                            foreach ($values as $value) {
                                $fullName = ltrim($value, ' ');
                                $q->orWhereRaw("CONCAT(first_name, ' ', last_name) = '$fullName'");
                            }
                        });
                    } else {
                        $query->whereRaw("CONCAT(first_name, ' ', last_name) = '$values'");
                    }
                })
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return User::class;
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
