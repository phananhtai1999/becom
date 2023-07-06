<?php

namespace App\Rules;

use App\Services\ParagraphTypeService;
use Illuminate\Contracts\Validation\Rule;

class ArticleContentRule implements Rule
{
    private $paragraphTypeUuid;

    /**
     * @param $paragraphTypeUuid
     */
    public function __construct($paragraphTypeUuid)
    {
        $this->paragraphTypeUuid = $paragraphTypeUuid;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $childrenParagraphType = optional(optional((new ParagraphTypeService())->findByUuid($this->paragraphTypeUuid))->childrenParagraphType)->count();
        foreach ($value as $item) {
            $results = json_decode($item, true);
            //Check value is array or not
            if (!is_array($results) || empty($results) || count($results) != $childrenParagraphType) {
                return false;
            }
            foreach ($results as $result) {
                if (empty($result['type_uuid']) || empty($result['content'])) {
                    return false;
                }
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
        return 'The :attribute must have type_uuid and content field and be equal to the number of children of paragraph type.';
    }
}
