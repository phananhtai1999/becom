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
        if ($user->deleted_at) {
            return [
                'code' => 1,
                'errors' => __('messages.account_deleted')
            ];
        }
        if ($user->banned_at) {
            return [
                'code' => 2,
                'errors' => __('messages.account_banned')
            ];
        }
        return true;
    }
}
