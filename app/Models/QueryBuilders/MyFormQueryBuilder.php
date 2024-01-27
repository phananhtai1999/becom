<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Form;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyFormQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Form::where([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()]
        ]);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Form())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'title',
                'contact_list_uuid',
                'user_uuid' .
                'template',
                'template_json',
                'publish_status',
                'display_type',
                'reject_reason',
                'html_template',
                'css_template',
                'js_template',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'title',
                'contact_list_uuid',
                'user_uuid' .
                'template',
                'template_json',
                'publish_status',
                'display_type',
                'reject_reason',
                'html_template',
                'css_template',
                'js_template',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'title',
                AllowedFilter::exact('exact__title', 'title'),
                'contact_list_uuid',
                AllowedFilter::exact('exact__contact_list_uuid', 'contact_list_uuid'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'template_json',
                AllowedFilter::exact('exact__template_json', 'template_json'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'contactList.name',
                AllowedFilter::exact('exact__contactList.name', 'contactList.name'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'reject_reason',
                AllowedFilter::exact('exact__reject_reason', 'reject_reason'),
                'display_type',
                AllowedFilter::exact('exact__display_type', 'display_type'),
                'html_template',
                AllowedFilter::exact('exact__html_template', 'html_template'),
                'css_template',
                AllowedFilter::exact('exact__css_template', 'css_template'),
                'js_template',
                AllowedFilter::exact('exact__js_template', 'js_template'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Form::class;
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
