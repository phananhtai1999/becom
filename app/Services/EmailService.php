<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Email;

class EmailService extends AbstractService
{
    protected $modelClass = Email::class;
}
