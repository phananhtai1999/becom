<?php

namespace App\Rules;

class CheckWebsitePagesRule
{
    public static function uniqueWebpageIds()
    {
        return function ($attribute, $value, $fail) {
            //Không cho phép trùng uuid của website pages
            $webpageIds = collect($value)->pluck('uuid')->toArray();

            if (count($webpageIds) !== count(array_unique($webpageIds))) {
                $fail('Duplicate website page uuids are not allowed.');
            }
        };
    }

    public static function singleHomepage()
    {
        return function ($attribute, $value, $fail) {
            $homepageCount = collect($value)
                ->filter(function ($webpage) {
                    return $webpage['is_homepage'];
                })->count();

            if ($homepageCount > 1) {
                $fail('Only one webpage can be set as homepage.');
            }
        };
    }
}
