<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelFilterDescriptionLanguageTrait
{
    /**
     * @param Builder $query
     * @param ...$descriptions
     * @return Builder
     */
    public function scopeDescription(Builder $query, ...$descriptions)
    {
        return $query->where(function ($q) use ($descriptions) {
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            foreach ($descriptions as $description) {
                $q->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(description, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(description, '$.$langDefault'))) like '%$description%'");
            }
        });
    }
}
