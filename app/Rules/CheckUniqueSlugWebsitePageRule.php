<?php

namespace App\Rules;

use App\Services\MyWebsitePageService;
use Illuminate\Contracts\Validation\Rule;

class CheckUniqueSlugWebsitePageRule implements Rule
{
    private $websitePageUuids;

    /**
     * @param $websitePageUuids
     */
    public function __construct($websitePageUuids)
    {
        $this->websitePageUuids = $websitePageUuids;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $uuids = collect($this->websitePageUuids)->pluck('uuid')->toArray();
        return (new MyWebsitePageService())->checkUniqueSlug($uuids);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The slug of each website page must be different.';
    }
}
