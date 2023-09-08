<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterKeywordLanguageTrait
{
    /**
     * @param Builder $query
     * @param ...$keywords
     * @return Builder
     */
    public function scopeKeyword(Builder $query, ...$keywords)
    {
        return $query->where(function ($q) use ($keywords) {
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            foreach ($keywords as $keyword) {
                $q->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(keyword, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(keyword, '$.$langDefault'))) like '%$keyword%'");
            }
        });
    }
}
