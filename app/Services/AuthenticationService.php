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
        if($user->banned_at){
            return [
                'errors' => __('messages.account_banned')
            ];
        }
        if ($user->deleted_at) {
            return [
                'errors' => __('messages.account_deleted')
            ];
        }
        return true;
    }
}
