<?php

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SortOpenTracking implements Sort
{

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->withCount('mailOpenTrackings')->orderByRaw("mail_open_trackings_count {$direction}");
    }
}
