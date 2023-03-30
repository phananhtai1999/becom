<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ContactUnsubscribe;
use App\Models\QueryBuilders\ContactUnsubscribeQueryBuilder;

class ContactUnsubscribeService extends AbstractService
{
    protected $modelClass = ContactUnsubscribe::class;
    protected $modelQueryBuilderClass = ContactUnsubscribeQueryBuilder::class;
}
