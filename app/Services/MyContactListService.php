<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ContactList;
use App\Models\QueryBuilders\MyContactListQueryBuilder;
use Illuminate\Support\Facades\DB;

class MyContactListService extends AbstractService
{
    protected $modelClass = ContactList::class;

    protected $modelQueryBuilderClass = MyContactListQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyContactListByKeyOrAbort($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return bool
     */
    public function checkMyContactList($id)
    {
        $contactList = $this->findOneWhere([
            ['uuid', $id],
            ['user_uuid', auth()->user()->getKey()],
        ]);

        if (!empty($contactList)) {
            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyContactListByKey($id)
    {
        $contactList = $this->findMyContactListByKeyOrAbort($id);

        return $this->destroy($contactList->getKey());
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function myTotalContactList($startDate, $endDate)
    {
        $totalMyContactList = DB::table('contact_lists')->selectRaw('count(uuid) as list')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->where('user_uuid', auth()->user()->getkey())
            ->get();

        return $totalMyContactList['0']->list;
    }

    public function myContactLists($request, $contactLists = [])
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $columns = $request->get('columns', '*');
        $pageName = $request->get('page_name', 'page');
        $search = $request->get('search', '');
        $searchBy = $request->get('search_by', '');

        if (empty($contactLists)) {

            return $this->modelQueryBuilderClass::searchQuery($search, $searchBy)
                ->paginate($perPage, $columns, $pageName, $page);
        }

        return $this->modelQueryBuilderClass::searchQuery($search, $searchBy)
            ->orWhereIn('uuid', $contactLists)
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
