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
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
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
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
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
        $totalMyContactList = $this->model->selectRaw('count(uuid) as list')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
            ->get();

        return $totalMyContactList['0']->list;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateTime
     * @return array
     */
    public function queryMyContactList($startDate, $endDate, $dateTime)
    {
        return $this->model->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, 0 as contact, count(uuid) as list")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
            ->orderBy('label', 'ASC')
            ->groupby('label')
            ->get()->toArray();
    }

    public function myContactLists($request, $contactLists = [])
    {
        $indexRequest = $this->getIndexRequest($request);

        if (empty($contactLists)) {

            return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
        }

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->orWhereIn('uuid', $contactLists)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
