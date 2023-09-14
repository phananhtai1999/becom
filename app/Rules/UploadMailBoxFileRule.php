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
        return is_file($value) && in_array($value->getClientOriginalExtension(), config('extension-file'));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a file and file of type: ' . implode(', ', config('extension-file'));
    }
}
