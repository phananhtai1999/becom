<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\User;

class UserService extends AbstractService
{
    protected $modelClass = User::class;
}
