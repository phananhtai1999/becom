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
        try {
            if ($search && !empty($searchBy)) {
                //Get all fields
                $getFillAble = app($baseQuery)->getFillable();
                $getTableName = app($baseQuery)->getTable();
                $query->where(function ($query) use ($search, $searchBy, $getFillAble, $getTableName, $baseQuery) {
                    foreach ($searchBy as $value) {
                        $query->when(in_array($value, $getFillAble), function ($q) use ($search, $value, $getTableName) {
                            $q->orWhere($getTableName . '.' . $value, 'like', '%' . $search . '%');
                        });
                        //Handle search in relational table
                        $query->when(!in_array($value, $getFillAble), function ($q) use ($search, $value, $baseQuery) {

                            $lastDotPosition = strrpos($value, '.');
                            $relationship = substr($value, 0, $lastDotPosition);
                            $columnName = substr($value, $lastDotPosition + 1);
                            //Check column name exist or not on relationship table
                            $relationshipModel = app($baseQuery)->$relationship();

                            if ($relationshipModel->getModel()->getConnection()->getSchemaBuilder()->hasColumn($relationshipModel->getModel()->getTable(), $columnName)) {
                                $q->orWhereHas($relationship, function ($q) use ($search, $columnName) {
                                    $q->where($columnName, 'like', '%' . $search . '%');
                                });
                            }
                        });
                    }
                });
            }

            return $query;
        } catch (\BadMethodCallException $exception) {
            return $query;
        }
    }
}
