<?php

namespace App\Rules;

use App\Services\TeamService;
use App\Services\UserTeamService;
use Illuminate\Contracts\Validation\Rule;

class ResetPasswordTeamMemberRule implements Rule
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
        $userTeam = optional((new UserTeamService())->findOneWhere([['user_uuid', $this->userUuid]]))->team_uuid;
        $teamCurrentUser = (new TeamService())->findOneWhere([
            ['uuid', $userTeam],
            ['owner_uuid', auth()->user()],
            ['app_id', auth()->appId()],
        ]);

        if ($userTeam && $teamCurrentUser)
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
        return 'The selected :attribute is invalid.';
    }
}
