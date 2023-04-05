<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterNameLanguageTrait
{
    /**
     * @param Builder $query
     * @param ...$name
     * @return Builder
     */
    public function scopeName(Builder $query, ...$name)
    {
        return $query->where(function ($q) use($name){
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            foreach ($name as $value) {
                $q->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(name, '$.$langDefault'))) like '%$value%'");
            }
        });
    }
}
