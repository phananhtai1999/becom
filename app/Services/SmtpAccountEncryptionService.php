<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SmtpAccountEncrytionQueryBuilder;
use App\Models\SmtpAccountEncryption;

class SmtpAccountEncryptionService extends AbstractService
{
    protected $modelClass = SmtpAccountEncryption::class;

    protected $modelQueryBuilderClass = SmtpAccountEncrytionQueryBuilder::class;
}
