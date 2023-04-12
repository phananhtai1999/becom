<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\DomainVerification;
use App\Models\QueryBuilders\DomainVerificationQueryBuilder;

class DomainVerificationService extends AbstractService
{
    protected $modelClass = DomainVerification::class;

    protected $modelQueryBuilderClass = DomainVerificationQueryBuilder::class;
}
