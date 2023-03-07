<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterLanguageTrait
{
    /**
     * @param Builder $query
     * @param $title
     * @return Builder
     */
    public function scopeTitle(Builder $query, $title)
    {
        $lang = app()->getLocale();
        return $query->where("title->$lang", 'like', "%$title%");
    }
}
