<?php

namespace App\Abstracts;

use App\Models\SearchQueryBuilders\SearchQueryBuilder;

abstract class AbstractQueryBuilder extends SearchQueryBuilder
{
    abstract public static function baseQuery();

    abstract public static function initialQuery();

    abstract public static function fillAble();

    abstract public static function searchQuery($search, $searchBy);
}
