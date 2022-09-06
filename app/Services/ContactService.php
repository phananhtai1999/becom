<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Contact;
use App\Models\QueryBuilders\ContactQueryBuilder;

class ContactService extends AbstractService
{
    protected $modelClass = Contact::class;

    protected $modelQueryBuilderClass = ContactQueryBuilder::class;
}
