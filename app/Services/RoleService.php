<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Role;

class RoleService extends AbstractService
{
    protected $modelClass = Role::class;
}
