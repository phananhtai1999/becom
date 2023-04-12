<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterExactNameLanguageTrait
{
    /**
     * @param Builder $query
     * @param ...$name
     * @return Builder
     */
    public function scopeExactName(Builder $query, ...$name)
    {
        return $query->where(function ($q) use($name){
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            foreach ($name as $value) {
                $q->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(name, '$.$langDefault'))) = '$value'");
            }
        });
    }
}
