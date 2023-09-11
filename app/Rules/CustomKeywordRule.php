<?php

namespace App\Rules;

use App\Services\WebsitePageService;
use Illuminate\Contracts\Validation\Rule;

class CustomKeywordRule implements Rule
{
    private $websitePageUuid;
    private $keyword;
    private $type;

    /**
     * @param $websitePageUuid
     * @param $keyword
     * @param $type
     */
    public function __construct($websitePageUuid, $keyword, $type)
    {
        $this->websitePageUuid = $websitePageUuid;
        $this->keyword = $keyword;
        $this->type = $type;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->type == 'website_page') {
            $websitePage = (new WebsitePageService())->findOneById($this->websitePageUuid);
        }
        if (($websitePage && !empty($websitePage->keywords['en'])) || ($websitePage && empty($websitePage->keywords['en']) && !empty($this->keyword['en']))) {

            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute.en field is default.';
    }
}
