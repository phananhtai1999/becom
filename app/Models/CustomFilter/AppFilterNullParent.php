<?php

namespace App\Models\CustomFilter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AppFilterNullParent implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if ($value === 'null') {
            return $query->whereNull($property);
        } elseif ($value === 'not_null') {
            return $query->whereNotNull($property);
        }

        return $query;
    }
}
