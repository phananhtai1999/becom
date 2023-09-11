<?php

namespace App\Rules;

use App\Services\WebsitePageService;
use Illuminate\Contracts\Validation\Rule;

class CustomDescriptionRule implements Rule
{
    private $websitePageUuid;
    private $description;
    private $keyword;

    /**
     * @param $websitePageUuid
     * @param $keyword
     * @param $description
     */
    public function __construct($websitePageUuid, $keyword, $description)
    {
        $this->websitePageUuid = $websitePageUuid;
        $this->keyword = $keyword;
        $this->description = $description;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $websitePage = (new WebsitePageService())->findOneById($this->websitePageUuid);
        if (($websitePage && !empty($websitePage->descriptions['en'])) || ($websitePage && empty($websitePage->descriptions['en']) && !empty($this->description['en'])) ||
            ($websitePage && empty($websitePage->descriptions['en']) && !empty($this->keyword['en']))) {

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
