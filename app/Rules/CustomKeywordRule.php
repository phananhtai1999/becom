<?php

namespace App\Rules;

use App\Services\WebsitePageService;
use Illuminate\Contracts\Validation\Rule;

class CustomKeywordRule implements Rule
{
    private $uuid;
    private $keyword;
    private $type;

    /**
     * @param $uuid
     * @param $keyword
     * @param $type
     */
    public function __construct($uuid, $keyword, $type)
    {
        $this->uuid = $uuid;
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
            $model = (new WebsitePageService())->findOneById($this->uuid);
        }
        if (($model && !empty($model->keywords['en'])) || ($model && empty($model->keywords['en']) && !empty($this->keyword['en']))) {

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
