<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ContactList;
use App\Models\QueryBuilders\ContactListQueryBuilder;

class ContactListService extends AbstractService
{
    protected $modelClass = ContactList::class;

    protected $modelQueryBuilderClass = ContactListQueryBuilder::class;

    /**
     * @param $model
     * @return array|void
     */
    public function findContactKeyByContactList($model)
    {
        $contacts = $model->contacts()->get();

        if (empty($contacts)) {

            return [];
        } else {
            foreach ($contacts as $contact) {
                $contactUuid[] = $contact->uuid;

                return $contactUuid;
            }
        }
    }
}
