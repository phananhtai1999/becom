<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterExactFieldTrait
{
    /**
     * @param Builder $query
     * @param ...$values
     * @return Builder
     */
    public function scopeExactField(Builder $query, ...$values)
    {
        return $query->where(function ($q) use ($values) {
            foreach ($values as $value) {
                $q->orWhere('name', '=', $value);
            }
        });
    }
}
