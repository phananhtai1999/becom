<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\ActivityHistory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return ActivityHistory::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new ActivityHistory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'type',
                'type_id',
                'content',
                'date',
                'contact_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'type',
                'type_id',
                'content',
                'date',
                'contact_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'type_id',
                AllowedFilter::exact('exact__type_id', 'type_id'),
                'content',
                AllowedFilter::exact('exact__content', 'content'),
                'date',
                AllowedFilter::exact('exact__date', 'date'),
                'contact_uuid',
                AllowedFilter::exact('exact__contact_uuid', 'contact_uuid'),
                AllowedFilter::scope('from__created_at'),
                AllowedFilter::scope('to__created_at'),
                AllowedFilter::scope('from__date'),
                AllowedFilter::scope('to__date'),
                AllowedFilter::callback("mailsendingHistory.status", function (Builder $query, $value) {
                    if (is_array($value)) {
                        $query->where(function ($query) use ($value) {
                            for ($i = 1; $i <= count($value); $i++) {
                                $query->orWhereExists(function ($query) use ($value, $i) {
                                    $query->select("mail_sending_history.uuid")
                                        ->from('mail_sending_history')
                                        ->whereRaw('activity_histories.type_id = mail_sending_history.uuid')
                                        ->where([
                                            ['activity_histories.type', '!=', 'remind'],
                                            ['activity_histories.type', '!=', 'note'],
                                            ['mail_sending_history.status', '=', $value[$i - 1]],
                                        ]);
                                });
                            }
                        });
                    } else {
                        $query->whereExists(function ($mailSendingHistory) use ($value) {
                            $mailSendingHistory->select("mail_sending_history.uuid")
                                ->from('mail_sending_history')
                                ->whereRaw('activity_histories.type_id = mail_sending_history.uuid')
                                ->where([
                                    ['activity_histories.type', '!=', 'remind'],
                                    ['activity_histories.type', '!=', 'note'],
                                    ['mail_sending_history.status', '=', $value],
                                ]);
                        });
                    }
                })
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return ActivityHistory::class;
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
