<?php

namespace App\Models\SearchQueryBuilders;

use Spatie\QueryBuilder\QueryBuilder;

class SearchQueryBuilder extends QueryBuilder
{
    /**
     * @param $baseQuery
     * @param $query
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public static function search($baseQuery, $query, $search, $searchBy)
    {
        if ($search && !empty($searchBy)) {
            //Get all fields
            $getFillAble = app($baseQuery)->getFillable();
            $query->where(function ($query) use ($search, $searchBy, $getFillAble) {
                foreach ($searchBy as $value) {
                    $query->when(in_array($value, $getFillAble), function ($q) use ($search, $value) {

                        return $q->orWhere($value, 'like', '%' . $search . '%');
                    });
                }
            });
        }

        return $query;
    }
}
