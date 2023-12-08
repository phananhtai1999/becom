<?php

namespace App\Rules;

use App\Models\Role;
use App\Models\Team;
use App\Services\ConfigService;
use Illuminate\Contracts\Validation\Rule;

class checkTeamOwnerRule implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!(new ConfigService())->checkUserRoles([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (Team::findOrFail($value)->owner_uuid != auth()->user()) {

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
        return 'You does not an owner to add child team';
    }
}
