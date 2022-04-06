<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\User;
use App\Models\UserAccessToken;
use App\Models\UserSocialProfile;
use Illuminate\Support\Facades\Cookie;

class UserAccessTokenService extends AbstractService
{
    protected $modelClass = UserAccessToken::class;

    /**
     * @param User $user
     * @return mixed
     */
    public function storeNewForUser(User $user)
    {
        return $this->create([
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip(),
            'user_uuid' => $user->getKey()
        ]);
    }

    /**
     * @return null
     */
    public function getCurrentUserKey()
    {
        if (Cookie::has('accessToken')) {
            $model = $this->findOneById(Cookie::get('accessToken'));

            if ($model) {
                return $model->user_uuid;
            }
        }

        return null;
    }
}
