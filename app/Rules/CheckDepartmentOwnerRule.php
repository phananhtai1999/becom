<?php

namespace App\Rules;

use App\Models\Department;
use App\Models\Role;
use App\Services\ConfigService;
use Illuminate\Contracts\Validation\Rule;

class CheckDepartmentOwnerRule implements Rule
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
        if (!(new ConfigService())->checkUserRoles([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (Department::findOrFail($value)->user_uuid != auth()->userId()) {

                return false;
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
        return 'You are not an owner to remove team out of department';
    }
}
