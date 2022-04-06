<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\User;

class UserService extends AbstractService
{
    protected $modelClass = User::class;

    /**
     * @return User|null
     */
    public function currentUser(): ?User
    {
        $userUUID = app(UserAccessTokenService::class)->getCurrentUserKey();

        if ($userUUID) {
            return $this->findOneById($userUUID);
        }

        return null;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function showByUserName($key)
    {
        return $this->model->where('username', $key)->firstOrFail();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function findByEmail($key)
    {
        return $this->model->where('email', $key)->first();
    }
}
