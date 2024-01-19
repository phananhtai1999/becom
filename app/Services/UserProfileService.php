<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserProfileQueryBuilder;
use App\Models\Role;
use App\Models\UserProfile;

class UserProfileService extends AbstractService
{
    protected $modelClass = UserProfile::class;

    protected $modelQueryBuilderClass = UserProfileQueryBuilder::class;

    public function checkCredit($creditNumber, $userUuid)
    {
        $user = $this->findOneWhereOrFail(['user_uuid' => $userUuid]);
        if ($user->credit < $creditNumber) {
            return false;
        }
        return true;
    }

    public function getMinCodeByNumberOfUser()
    {
        $min = $power = 6;
        $lastUser = $this->model->count();
        $nextPower = pow(10, $power + 1);
        while ($lastUser >= $nextPower) {
            $min++;
            $power++;
            $nextPower = pow(10, $power + 1);
        }

        return $min;
    }

    public function getCurrentUserRole(): string
    {
        if (auth()->hasRole([Role::ROLE_ROOT])) {
            $char = 'r' . auth()->userId();
        } elseif (auth()->hasRole([Role::ROLE_ADMIN])) {
            $char = 'a' . auth()->userId();
        } elseif (auth()->hasRole([Role::ROLE_EDITOR])) {
            $char = 'e' . auth()->userId();
        } else {
            $char = 'u' . auth()->userId();
        }

        return $char;
    }

    public function checkLanguagesPermission()
    {
        if (auth()->guest()) {
            return false;
        }

        return auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN, Role::ROLE_EDITOR]);
    }

    public function checkLanguagesPermissionWithAdminAndRootRole()
    {
        if (auth()->guest()) {
            return false;
        }

        return auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN]);
    }
}
