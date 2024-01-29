<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckDepartmentBelongToBusiness implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $businesses = auth()->user()->businessManagements;
        if ($businesses->isNotEmpty()) {
            if ($businesses->first()->locations->isNotEmpty()) {
                foreach ($businesses->first()->locations as $location) {
                    $department = $location->departments;
                    if ($department->isNotEmpty() && in_array($value, $department->pluck('uuid')->toArray())) {
                        return true;
                    }
                }
            }
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
        return 'The department not in your business Or you don not have business.';
    }
}
