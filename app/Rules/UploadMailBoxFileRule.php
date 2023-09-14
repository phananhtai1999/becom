<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UploadMailBoxFileRule implements Rule
{
    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_file($value) && in_array($value->getClientOriginalExtension(), ['mp4', 'jpeg', 'png', 'jpg', 'doc', 'docx']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a file and file of type: mp4, jpeg, png, jpg, doc, docx.';
    }
}
