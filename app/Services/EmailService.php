<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Email;
use App\Models\QueryBuilders\EmailQueryBuilder;

class EmailService extends AbstractService
{
    protected $modelClass = Email::class;

    protected $modelQueryBuilderClass = EmailQueryBuilder::class;
}
