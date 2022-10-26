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
}
