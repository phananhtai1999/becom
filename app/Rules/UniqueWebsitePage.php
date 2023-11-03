<?php

namespace App\Rules;

use App\Models\WebsitePage;
use Illuminate\Contracts\Validation\Rule;

class UniqueWebsitePage implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($websitePageUuids)
    {
        $this->websitePages = $websitePageUuids;    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $types = [];
        $uuids = collect($this->websitePages)->pluck('uuid')->toArray();
        foreach ($uuids as $uuid) {
            $page = WebsitePage::where('uuid', $uuid)->first();
            if ($page && $page->type != WebsitePage::STATIC_TYPE) {
                $type = $page->type;
                if (in_array($type, $types)) {

                    return false;
                }

                $types[] = $type;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'In one website must be only have 1 news.home, 1 news.detail, 1 news.category';
    }
}
