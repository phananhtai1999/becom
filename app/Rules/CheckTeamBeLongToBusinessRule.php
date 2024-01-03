<?php

namespace App\Rules;

use App\Models\Location;
use App\Models\Role;
use Illuminate\Contracts\Validation\Rule;

class CheckTeamBeLongToBusinessRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (auth()->user()->roles->whereNotIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->first()) {
            $businesses = auth()->user()->businessManagements;
            if (!$businesses->toArray()) {

                return false;
            } else {
                if ($businesses->first()->teams->isEmpty() || $businesses->first()->teams->where('uuid', $value)->isEmpty()) {

                    return false;
                }
            }

            return true;
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
        return 'You do not have permission';
    }
}
