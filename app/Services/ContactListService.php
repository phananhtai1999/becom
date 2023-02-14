<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ContactList;
use App\Models\QueryBuilders\ContactListQueryBuilder;
use App\Models\QueryBuilders\SortContactsInContactListQueryBuilder;

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

    /**
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $sort
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|void
     */
    public function sortContacts($perPage, $columns, $pageName,  $page, $sort)
    {
        if ($sort == 'contacts') {
            return SortContactsInContactListQueryBuilder::initialQuery()->withCount('contacts')->orderBy('contacts_count')->paginate($perPage, $columns, $pageName, $page);
        } elseif ($sort == '-contacts') {
            return SortContactsInContactListQueryBuilder::initialQuery()->withCount('contacts')->orderByDesc('contacts_count')->paginate($perPage, $columns, $pageName, $page);
        }
    }
}
