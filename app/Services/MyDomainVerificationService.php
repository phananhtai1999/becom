<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\DomainVerification;
use App\Models\QueryBuilders\MyDomainVerificationQueryBuilder;

class MyDomainVerificationService extends AbstractService
{
    protected $modelClass = DomainVerification::class;

    protected $modelQueryBuilderClass = MyDomainVerificationQueryBuilder::class;
}
