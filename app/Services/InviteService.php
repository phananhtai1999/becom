<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Invite;
use App\Models\QueryBuilders\InviteQueryBuilder;

class InviteService extends AbstractService
{
    protected $modelClass = Invite::class;

    protected $modelQueryBuilderClass = InviteQueryBuilder::class;
}
