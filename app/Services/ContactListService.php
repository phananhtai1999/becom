<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ContactList;
use App\Models\QueryBuilders\ContactListQueryBuilder;

class ContactListService extends AbstractService
{
    protected $modelClass = ContactList::class;

    protected $modelQueryBuilderClass = ContactListQueryBuilder::class;
}
