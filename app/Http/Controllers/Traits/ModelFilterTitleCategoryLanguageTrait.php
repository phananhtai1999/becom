<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterTitleCategoryLanguageTrait
{
    /**
     * @param Builder $query
     * @param ...$title
     * @return Builder
     */
    public function scopeTitleCategory(Builder $query, ...$title)
    {
        return $query->where(function ($q) use($title){
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            foreach ($title as $value) {
                $q->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(title, '$.$langDefault'))) like '%$value%'");
            }
        });
    }
}
