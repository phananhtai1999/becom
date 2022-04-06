<?php

namespace App\Services;

use App\Models\User;

class AuthenticationService
{
    /**
     * @param User $user
     * @return array|bool
     */
    public function doesUserCanLogin(User $user)
    {
        if ($user->is_deleted) {
            return [
                'errors' => __('Your account is no longer available in the system')
            ];
        }

        return true;
    }
}
