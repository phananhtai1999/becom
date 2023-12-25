<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserProfileQueryBuilder;
use App\Models\UserProfile;

class UserProfileService extends AbstractService
{
    protected $modelClass = UserProfile::class;

    protected $modelQueryBuilderClass = UserProfileQueryBuilder::class;
}
