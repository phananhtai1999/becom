<?php

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SortContacts implements Sort
{
    /**
     * @param Builder $query
     * @param bool $descending
     * @param string $property
     * @return void
     */
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->withCount('contacts')->orderByRaw("contacts_count {$direction}");
    }
}
