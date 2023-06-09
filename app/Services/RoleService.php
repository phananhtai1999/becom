<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\RoleQueryBuilder;
use App\Models\Role;

class RoleService extends AbstractService
{
    protected $modelClass = Role::class;

    protected $modelQueryBuilderClass = RoleQueryBuilder::class;

    public function showRoleOfAdminById($id)
    {
        return $this->findOneWhereOrFail([
            ['name', '<>', Role::ROLE_ROOT],
            ['uuid', $id]
        ]);
    }
}
