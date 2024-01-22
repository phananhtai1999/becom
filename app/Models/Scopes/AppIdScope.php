<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AppIdScope implements Scope
{
    public static $isEnabled = true;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (self::$isEnabled && in_array('app_id', $model->getFillable()) && auth()->appId()) {
            $builder->where("{$model->getTable()}.app_id", auth()->appId());
        }
    }
}
