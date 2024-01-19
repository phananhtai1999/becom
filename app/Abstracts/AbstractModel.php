<?php

namespace App\Abstracts;

use App\Models\Scopes\AppIdScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    use HasAttributes;

    protected static function booted()
    {
        static::addGlobalScope(new AppIdScope);
    }

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

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeFromUpdatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('updated_at', '>=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeToUpdatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('updated_at', '<=', $date);
    }
}
