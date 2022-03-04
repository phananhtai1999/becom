<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;

class MailTemplateService extends AbstractService
{
    protected $modelClass = MailTemplate::class;
}
