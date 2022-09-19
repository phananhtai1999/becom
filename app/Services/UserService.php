<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserQueryBuilder;
use App\Models\User;

class UserService extends AbstractService
{
    protected $modelClass = User::class;

    protected $modelQueryBuilderClass = UserQueryBuilder::class;

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

    /**
     * @param $email
     * @return mixed
     */
    public function findUserLogin($email)
    {
        return $this->model->withTrashed()->where([
            'email' => $email
        ])->first();
    }

    /**
     * @param $contactsNumberSendEmail
     * @param $userUuid
     * @return bool
     */
    public function checkCreditToSendCEmail($creditNumberSendEmail, $userUuid)
    {
        $user = $this->findOneById($userUuid);
        if($user->credit < $creditNumberSendEmail) {
            return false;
        }
        return true;
    }
}
