<?php

namespace App\Services;

use App\Models\PasswordReset;
use App\Abstracts\AbstractService;

class PasswordResetService extends AbstractService
{
    protected $modelClass = PasswordReset::class;
}
