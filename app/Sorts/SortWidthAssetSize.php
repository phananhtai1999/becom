<?php

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SortWidthAssetSize implements Sort
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

        $query->join('asset_sizes', 'asset_sizes.uuid', '=', 'assets.asset_size_uuid')
            ->orderBy('asset_sizes.width', $direction);
    }
}
