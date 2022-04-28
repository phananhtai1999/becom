<?php

namespace App\Abstracts;

use Spatie\QueryBuilder\QueryBuilder;

abstract class AbstractQueryBuilder extends QueryBuilder
{
    abstract public static function baseQuery();

    abstract public static function initialQuery();
}
