<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterFieldTrait
{
    /**
     * @param Builder $query
     * @param ...$values
     * @return Builder
     */
    public function scopeField(Builder $query, ...$values)
    {
        return $query->where(function ($q) use ($values) {
            foreach ($values as $value) {
                $q->orWhere('name', 'like', '%' . $value . '%');
            }
        });
    }
}
