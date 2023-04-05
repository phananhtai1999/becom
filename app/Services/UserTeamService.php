<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserTeam;

class UserTeamService extends AbstractService
{
    protected $modelClass = UserTeam::class;
}
