<?php

namespace App\Rules;

use App\Services\ArticleCategoryService;
use App\Services\ArticleService;
use App\Services\WebsitePageService;
use Illuminate\Contracts\Validation\Rule;

class CustomDescriptionRule implements Rule
{
    private $uuid;
    private $description;
    private $keyword;
    private $type;

    /**
     * @param $uuid
     * @param $keyword
     * @param $description
     * @param $type
     */
    public function __construct($uuid, $keyword, $description, $type)
    {
        $this->uuid = $uuid;
        $this->keyword = $keyword;
        $this->description = $description;
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
        } elseif ($this->type == 'articles') {
            $model = (new ArticleService())->findOneById($this->uuid);
        } else {
            $model = (new ArticleCategoryService())->findOneById($this->uuid);
        }

        if (($model && !empty($model->descriptions['en'])) || ($model && empty($model->descriptions['en']) && !empty($this->description['en'])) ||
            ($model && empty($model->descriptions['en']) && !empty($this->keyword['en']))) {

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
