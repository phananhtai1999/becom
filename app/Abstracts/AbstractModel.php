<?php

namespace App\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    use HasAttributes;

    public function scopeFromCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeToCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', '<=', $date);
    }
}
