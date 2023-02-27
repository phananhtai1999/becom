<?php

namespace App\Models\SearchQueryBuilders;

use App\Models\User;
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
            $getTableName = app($baseQuery)->getTable();
            $query->where(function ($query) use ($search, $searchBy, $getFillAble, $getTableName) {
                foreach ($searchBy as $value) {
                    $query->when(in_array($value, $getFillAble), function ($q) use ($search, $value, $getTableName) {

                        return $q->orWhere($getTableName . '.'  . $value, 'like', '%' . $search . '%');
                    });
                }
            });
        }

        return $query;
    }
}
