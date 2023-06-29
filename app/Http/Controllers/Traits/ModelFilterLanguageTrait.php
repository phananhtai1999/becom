<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterLanguageTrait
{
    /**
     * @param Builder $query
     * @param ...$titles
     * @return Builder
     */
    public function scopeTitle(Builder $query, ...$titles)
    {
        return $query->where(function ($q) use($titles){
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            foreach ($titles as $title) {
                $q->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(title, '$.$langDefault'))) like '%$title%'");
            }
        });
    }
}
