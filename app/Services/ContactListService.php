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
     * @return array
     */
    public function findContactKeyByContactList($model)
    {
        return $model->contacts()->pluck('uuid')->all();
    }

    /**
     * @param $id
     * @return bool
     */
    public function checkExistsContactListInTables($id)
    {
        $contactList = $this->findOrFailById($id);

        $campaigns = $contactList->campaigns->toArray();

        if (!empty($campaigns)) {
            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function checkContactListByUuid($id)
    {
        $contactList = $this->findOneWhere(['uuid' => $id ]);

        if (!empty($contactList)) {
            return true;
        }

        return false;
    }

}
