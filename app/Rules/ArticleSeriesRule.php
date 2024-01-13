<?php

namespace App\Rules;

use App\Models\Role;
use App\Services\ParagraphTypeService;
use App\Services\UserProfileService;
use Illuminate\Contracts\Validation\Rule;

class ArticleSeriesRule implements Rule
{
    private $userUuid;

    /**
     * @param $userUuid
     */
    public function __construct($userUuid)
    {
        $this->userUuid = $userUuid;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = (new  UserProfileService())->findOneWhereOrFail(['user_uuid' => $value]);
        $role = optional(optional(optional($user)->roles)->whereIn('slug', [Role::ROLE_ADMIN, Role::ROLE_ROOT]))->count();
        $roleEditor = optional(optional(optional($user)->roles)->whereIn('slug', [Role::ROLE_EDITOR]))->count();

        if ($user && $roleEditor && !$role)
        {
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
        return 'The :attribute must exist and role is editor.';
    }
}
