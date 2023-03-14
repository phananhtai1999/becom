<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Contact;
use App\Models\QueryBuilders\MyContactQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MyContactService extends AbstractService
{
    protected $modelClass = Contact::class;

    protected $modelQueryBuilderClass = MyContactQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyContactByKeyOrAbort($id)
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
    public function deleteMyContactByKey($id)
    {
        $contact = $this->findMyContactByKeyOrAbort($id);

        return $this->destroy($contact->getKey());
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function myTotalContact($startDate, $endDate)
    {
        $totalMyContact = DB::table('contacts')->selectRaw('count(uuid) as contact')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->where('user_uuid', auth()->user()->getkey())
            ->get();

        return $totalMyContact['0']->contact;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateTime
     * @return array
     */
    public function queryMyContact($startDate, $endDate, $dateTime)
    {
        return DB::table('contacts')->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, count(uuid) as contact, 0 as list")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->where('user_uuid',auth()->user()->getkey())
            ->orderBy('label', 'ASC')
            ->groupby('label')
            ->get()->toArray();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateTime
     * @return array
     */
    public function queryMyContactList($startDate, $endDate, $dateTime)
    {
        return DB::table('contact_lists')->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, 0 as contact, count(uuid) as list")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->where('user_uuid',auth()->user()->getkey())
            ->orderBy('label', 'ASC')
            ->groupby('label')
            ->get()->toArray();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateFormat
     * @param $type
     * @return array
     */
    public function createQueryGetIncrease($startDate, $endDate, $dateFormat, $type)
    {
        $currentUser = auth()->user()->getkey();
        $string = $type === "month" ? "-01" : "";
        $todaySmtpAccountTableSubQuery = $yesterdaySmtpAccountTableSubQuery = "(SELECT date_format(created_at, '{$dateFormat}') as date_field, COUNT(uuid) as createContact
                  from contacts
                  where date(created_at) >= '{$startDate}' and date(created_at) <= '{$endDate}' and deleted_at is NULL and user_uuid = '{$currentUser}'
                  GROUP By date_field)";

        return DB::table(DB::raw("$todaySmtpAccountTableSubQuery as today"))->selectRaw("today.date_field, today.createContact, (today.createContact - yest.createContact) as increase")
            ->leftJoin(DB::raw("$yesterdaySmtpAccountTableSubQuery as yest"), 'yest.date_field', '=', DB::raw("date_format(concat(today.date_field, '$string') - INTERVAL 1 {$type}, '{$dateFormat}')"))
            ->get()->toArray();
    }

    /**
     * @param $groupBy
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function myContactChart($groupBy, $startDate, $endDate)
    {
        $parseStartDate = Carbon::parse($startDate);
        $dateTime = $chartResult = $data = $result = [];
        if ($groupBy === 'hour') {
            $dateFormat = "%Y-%m-%d %H:00:00";
            $subDate = Carbon::parse($startDate)->subDay();
            $myContactLists = $this->queryMyContactList($startDate, $endDate, "%Y-%m-%d %H:00:00");
            $parseEndDate = Carbon::parse($endDate)->endOfDay();
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d H:00:00'),
                ];
                $parseStartDate->addHour();
            }
        } elseif ($groupBy === 'date') {
            $dateFormat = "%Y-%m-%d";
            $subDate = Carbon::parse($startDate)->subDay();
            $myContactLists = $this->queryMyContactList($startDate, $endDate, "%Y-%m-%d");
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d'),
                ];
                $parseStartDate->addDay();
            }
        } elseif ($groupBy === 'month') {
            $dateFormat = "%Y-%m";
            $subDate = Carbon::parse($startDate)->subMonth();
            $myContactLists = $this->queryMyContactList($startDate, $endDate, "%Y-%m");
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m'),
                ];
                $parseStartDate->addMonth();
            }
        }

        $myContacts = $this->queryMyContact($subDate, $parseEndDate, $dateFormat);
        $myContactsIncrease = $this->createQueryGetIncrease($subDate, $endDate, $dateFormat, $groupBy === 'date' ? 'day' : $groupBy);
        if (!empty($myContacts)) {
            foreach ($myContacts as $myContact) {
                foreach ($myContactsIncrease as $myContactIncrease) {
                    if (in_array($myContactIncrease->date_field, [$myContact->label])) {
                        $chartResult[] = [
                            'label' => $myContact->label,
                            'contact' => $myContact->contact,
                            'list' => $myContact->list,
                            'increase' => $myContactIncrease->increase
                        ];
                    }
                }
            }
        }

        $lastIncrease = 0;
        foreach ($dateTime as $value) {
            if (!empty($chartResult)) {
                foreach ($chartResult as $chartItem) {
                    if (in_array($value['date_time'], $chartItem)) {
                        $data[] = [
                            'label' => $value['date_time'],
                            'contact' => $chartItem['contact'],
                            'list' => $chartItem['list'],
                            'increase' => $chartItem['increase'] ?? $chartItem['contact']
                        ];
                        $lastIncrease = $chartItem['contact'];
                        $check = true;
                        break;
                    } else {
                        if ($groupBy === 'hour') {
                            $prevTime = Carbon::parse($value['date_time'])->subHour()->toDateTimeString();
                        }
                        if ($groupBy === 'date') {
                            $prevTime = Carbon::parse($value['date_time'])->subDay()->toDateString();
                        }
                        if ($groupBy === 'month') {
                            $prevTime = Carbon::parse($value['date_time'])->subMonth()->format('Y-m');
                        }
                        if (in_array($prevTime, $chartItem)) {
                            $lastIncrease = $chartItem['contact'];
                        }
                        $check = false;
                    }
                }
                if (!($check)) {
                    $data[] = [
                        'label' => $value['date_time'],
                        'contact' => 0,
                        'list' => 0,
                        'increase' => -$lastIncrease
                    ];
                    $lastIncrease = 0;
                }
            } else {
                $data [] = [
                    'label' => $value['date_time'],
                    'contact' => 0,
                    'list' => 0,
                    'increase' => 0
                ];
            }
        }

        foreach ($data as $item) {
            if (!empty($myContactLists)) {
                foreach ($myContactLists as $myContactList) {
                    if (in_array($myContactList->label, [$item['label']])) {
                        $check = true;
                        $result [] = [
                            'label' => $item['label'],
                            'contact' => $item['contact'],
                            'list' => $myContactList->list,
                            'increase' => $item['increase'],
                        ];
                        break;
                    } else {
                        $check = false;
                    }
                }
                if (!($check)) {
                    $result [] = [
                        'label' => $item['label'],
                        'contact' => $item['contact'],
                        'list' => $item['list'],
                        'increase' => $item['increase'],
                    ];
                }
            } else {
                $result [] = [
                    'label' => $item['label'],
                    'contact' => $item['contact'],
                    'list' => $item['list'],
                    'increase' => $item['increase'],
                ];
            }
        }

        return $result;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $contactListUuid
     * @return int
     */
    public function getTotalPointsContactByMyContactList($startDate, $endDate, $contactListUuid = null)
    {
        $totalMyContact = $this->model->selectRaw('sum(contacts.points) as points')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->where('contact_lists.user_uuid', auth()->user()->getkey())
            ->when($contactListUuid, function ($query, $contactListUuid) {
                $query->where('contact_lists.uuid', $contactListUuid);
            })
            ->whereNull('contact_lists.deleted_at')
            ->whereDate('contacts.updated_at', '>=', $startDate)
            ->whereDate('contacts.updated_at', '<=', $endDate)
            ->first();

        return (int) $totalMyContact->points;
    }

    /**
     * @param $dateFormat
     * @param $startDate
     * @param $endDate
     * @param $type
     * @param $contactListUuid
     * @return array
     */
    public function createQueryGetPointsContactByMyContactList($dateFormat, $startDate, $endDate, $type, $contactListUuid)
    {
//        SELECT today.label, today.pointsContact, (today.pointsContact - yest.pointsContact) as increase
//FROM (SELECT date_format(updated_at, '%Y-%m-%d') as label, SUM(points) as pointsContact
//from contacts
//where date(updated_at) >= '2022-11-21' AND date(updated_at) <= '2022-11-24'
//GROUP By label) today LEFT JOIN
//    (SELECT date_format(updated_at, '%Y-%m-%d') as label, SUM(points) as pointsContact
//from contacts
//where date(updated_at) >= '2022-11-21' AND date(updated_at) <= '2022-11-24'
//GROUP By label) yest On yest.label = today.label - INTERVAL 1 day;
        $queryContactList = !empty($contactListUuid) ? "and cl.uuid = '{$contactListUuid}'" : "";
        $currentUser = auth()->user()->getkey();
        $string = $type === "month" ? "-01" : "";
        $todayPointsContactTableSubQuery = $yesterdayPointsContactTableSubQuery = "(SELECT date_format(c.updated_at, '{$dateFormat}') as label, sum(c.points) as points
                  from contacts c, contact_contact_list ccl, contact_lists cl
                  where c.uuid = ccl.contact_uuid AND ccl.contact_list_uuid = cl.uuid and
                  date(c.updated_at) >= '{$startDate}' and date(c.updated_at) <= '{$endDate}'
                  {$queryContactList}
                  and cl.user_uuid = '{$currentUser}'
                  and c.deleted_at is NULL and cl.deleted_at is NULL
                  GROUP By label)";

        return DB::table(DB::raw("$todayPointsContactTableSubQuery as today"))->selectRaw("today.label, today.points, (today.points - yest.points) as increase")
            ->leftJoin(DB::raw("$yesterdayPointsContactTableSubQuery as yest"), 'yest.label', '=', DB::raw("date_format(concat(today.label, '$string') - INTERVAL 1 {$type}, '{$dateFormat}')"))
            ->get()->toArray();
    }

    /**
     * @param $groupBy
     * @param $startDate
     * @param $endDate
     * @param $contactListUuid
     * @return array
     */
    public function getPointsContactChartByMyContactList($groupBy, $startDate, $endDate, $contactListUuid = null)
    {
        $times = $result = [];
        $check = true;
        $subDate = $startDate;
        $startDate = Carbon::parse($startDate);

        if($groupBy === "hour"){
            $dateFormat = "%Y-%m-%d %H:00:00";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d H:00:00');
                $startDate = $startDate->addHour();
            }
        }
        if($groupBy === "date"){
            $dateFormat = "%Y-%m-%d";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d');
                $startDate = $startDate->addDay();
            }
        }
        if($groupBy === "month"){
            $dateFormat = "%Y-%m";
            $subDate = Carbon::parse($subDate)->subMonth();
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m');
                $startDate = $startDate->addMonth();
            }
        }

        $pointsContactsChart = $this->createQueryGetPointsContactByMyContactList($dateFormat, $subDate, $endDate, $groupBy === 'date' ? 'day' : $groupBy, $contactListUuid);

        $lastIncrease = 0;
        foreach ($times as $time){
            if(!empty($pointsContactsChart)) {
                foreach ($pointsContactsChart as $pointsContactChart){
                    if($time == $pointsContactChart->label) {
                        $result[] = [
                            'label' => $pointsContactChart->label,
                            'points' => (int)$pointsContactChart->points,
                            'increase' => (int) ($pointsContactChart->increase ?? $pointsContactChart->points)
                        ];
                        $check = true;
                        break;
                    }else{
                        $prevTime = $time;
                        if($groupBy === 'hour'){
                            $prevTime = Carbon::parse($prevTime)->subHour()->toDateTimeString();
                        }
                        if($groupBy === 'date'){
                            $prevTime = Carbon::parse($prevTime)->subDay()->toDateString();
                        }
                        if($groupBy === 'month'){
                            $prevTime = Carbon::parse($prevTime)->subMonth()->format('Y-m');
                        }
                        if($prevTime == $pointsContactChart->label){
                            $lastIncrease = $pointsContactChart->points;
                        }
                        $check = false;
                    }
                }
                if(!$check){
                    $result[] = [
                        'label' => $time,
                        'points' => 0,
                        'increase' => -$lastIncrease
                    ];
                    $lastIncrease = 0;
                }
            }else{
                $result[] = [
                    'label' => $time,
                    'points' => 0,
                    'increase' => 0

                ];
            }
        }
        return $result;
    }

    /**
     * @return QueryBuilder
     */
    public function filteringByMyCustomContactField()
    {
        $modelKeyName = $this->model->getKeyName();

        return QueryBuilder::for($this->model->where('user_uuid', auth()->user()->getkey()))
            ->allowedFields([
                $modelKeyName,
                'email',
                'first_name',
                'last_name',
                'middle_name',
                'points',
                'phone',
                'sex',
                'dob',
                'city',
                'country',
                'avatar',
                'status_uuid',
                'user_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'email',
                'first_name',
                'last_name',
                'middle_name',
                'points',
                'phone',
                'sex',
                'dob',
                'city',
                'country',
                'avatar',
                'status_uuid',
                'user_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'middle_name',
                AllowedFilter::exact('exact__middle_name', 'middle_name'),
                'points',
                AllowedFilter::exact('exact__points', 'points'),
                'phone',
                AllowedFilter::exact('exact__phone', 'phone'),
                'sex',
                AllowedFilter::exact('exact__sex', 'sex'),
                'dob',
                AllowedFilter::exact('exact__dob', 'dob'),
                'city',
                AllowedFilter::exact('exact__city', 'city'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'avatar',
                AllowedFilter::exact('exact__avatar', 'avatar'),
                'status_uuid',
                AllowedFilter::exact('exact__status_uuid', 'status_uuid'),
                'status.name',
                AllowedFilter::exact('exact__status.name', 'status.name'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                AllowedFilter::scope('from__dob'),
                AllowedFilter::scope('to__dob'),
                $this->getMyDuplicateFiltersByNumeric($modelKeyName),
                $this->getMyDuplicateFilters('email'),
                $this->getMyDuplicateFilters('first_name'),
                $this->getMyDuplicateFilters('last_name'),
                $this->getMyDuplicateFilters('middle_name'),
                $this->getMyDuplicateFilters('country'),
                $this->getMyDuplicateFilters('city'),
                $this->getMyDuplicateFilters('phone'),
                $this->getMyDuplicateFilters('sex'),
                $this->getMyDuplicateFiltersByNumeric('points'),
                $this->getMyDuplicateFiltersByNumeric('dob'),
                $this->getMyDuplicateFiltersByNumeric('user_uuid'),
                $this->getMyFilterRelationshipWithUser('user.username'),
            ]);
    }

    /**
     * @param $search
     * @param $searchBy
     * @return QueryBuilder
     */
    public function search($search, $searchBy): QueryBuilder
    {
        if ($search && !empty($searchBy)) {
            //Get all fields
            $getFillAble = app(Contact::class)->getFillable();
            $query = $this->filteringByMyCustomContactField();
            $query->where(function ($query) use ($search, $searchBy, $getFillAble) {
                foreach ($searchBy as $value) {
                    $query->when(in_array($value, $getFillAble), function ($q) use ($search, $value) {

                        return $q->orWhere($value, 'like', '%' . $search . '%');
                    });
                }
            });

            return $query;
        }

        return $this->filteringByMyCustomContactField();
    }

    /**
     * @param $field
     * @return AllowedFilter
     */
    public function getMyDuplicateFilters($field)
    {
        return AllowedFilter::callback("custom__$field", function (Builder $query, $value, $field) {
            if ($value[0] == $field) {
                if ($value[1] == '=') {
                    $query->whereIn($field, array_slice($value, 3));
                } elseif ($value[1] == '!=') {
                    $query->whereNotIn($field, array_slice($value, 3));
                } elseif ($value[1] == 'like') {
                    if (count($value) > 4) {
                        $query->where(function ($query) use ($value, $field) {
                            for ($i = 4; $i <= count($value); $i++) {
                                $query->orWhere($field, 'like', '%' . $value[$i - 1] . '%');
                            }
                        });
                    } else {
                        $query->where($field, 'like', '%' . $value[3] . '%');
                    }
                } elseif ($value[1] == 'empty') {
                    $query->whereNull($field);
                } elseif ($value[1] == '!empty') {
                    $query->whereNotNull($field);
                }
            }
        }, $field);
    }

    /**
     * @param $field
     * @return AllowedFilter
     */
    public function getMyDuplicateFiltersByNumeric($field)
    {
        return AllowedFilter::callback("custom__$field", function (Builder $query, $value, $field) {
            if ($value[0] == $field) {
                if ($value[1] == '=') {
                    $query->whereIn($field, array_slice($value, 3));
                } elseif ($value[1] == '!=') {
                    $query->whereNotIn($field, array_slice($value, 3));
                } elseif ($value[1] == 'like') {
                    if (count($value) > 4) {
                        $query->where(function ($query) use ($value, $field) {
                            for ($i = 4; $i <= count($value); $i++) {
                                $query->orWhere($field, 'like', '%' . $value[$i - 1] . '%');
                            }
                        });
                    } else {
                        $query->where($field, 'like', '%' . $value[3] . '%');
                    }
                } elseif ($value[1] == '>') {
                    $query->where($field, '>', max(array_slice($value, 3)));
                } elseif ($value[1] == '>=') {
                    $query->where($field, '>=', min(array_slice($value, 3)));
                } elseif ($value[1] == '<') {
                    $query->where($field, '<', min(array_slice($value, 3)));
                } elseif ($value[1] == '<=') {
                    $query->where($field, '<=', max(array_slice($value, 3)));
                } elseif ($value[1] == 'empty') {
                    $query->whereNull($field);
                } elseif ($value[1] == '!empty') {
                    $query->whereNotNull($field);
                }
            }
        }, $field);
    }

    /**
     * @param $field
     * @return AllowedFilter
     */
    public function getMyFilterRelationshipWithUser($field)
    {
        return AllowedFilter::callback("custom__$field", function (Builder $query, $value, $field) {
            if ($value[0] == $field) {
                if ($value[1] == '=') {
                    $query->whereExists(function ($user) use ($value) {
                        $user->from('users')
                            ->whereRaw('contacts.user_uuid = users.uuid')
                            ->whereIn('users.username', array_slice($value, 3));
                    });
                } elseif ($value[1] == '!=') {
                    $query->whereExists(function ($user) use ($value) {
                        $user->from('users')
                            ->whereRaw('contacts.user_uuid = users.uuid')
                            ->whereNotIn('users.username', array_slice($value, 3));
                    });
                } elseif ($value[1] == 'like') {
                    if (count($value) > 4) {
                        $query->where(function ($query) use ($value) {
                            for ($i = 4; $i <= count($value); $i++) {
                                $query->orWhereExists(function ($query) use ($value, $i) {
                                    $query->select("users.uuid")
                                        ->from('users')
                                        ->whereRaw('contacts.user_uuid = users.uuid')
                                        ->where('users.username', 'like', '%' . $value[$i - 1] . '%');
                                });
                            }
                        });
                    } else {
                        $query->whereExists(function ($user) use ($value) {
                            $user->select("users.uuid")
                                ->from('users')
                                ->whereRaw('contacts.user_uuid = users.uuid')
                                ->where('users.username', 'like', '%' . $value[3] . '%');
                        });
                    }
                }
            }
        }, $field);
    }

    /**
     * @param $uuidsIn
     * @param $uuidsNotIn
     * @param $perPage
     * @return LengthAwarePaginator|QueryBuilder
     */
    public function sortMyContactsToTopOrBottomOfListByUuid($uuidsIn, $uuidsNotIn, $perPage, $search, $searcBy)
    {
        $arrayIntersectUuidsIn = array_intersect(explode(',', $uuidsIn), $this->model->all()->pluck('uuid')->toArray());
        $arrayIntersectUuidsNotIn = array_intersect(explode(',', $uuidsNotIn), $this->model->all()->pluck('uuid')->toArray());
        if (!empty($uuidsIn) && !empty($uuidsNotIn) && !empty($arrayIntersectUuidsIn) && !empty($arrayIntersectUuidsNotIn)) {

            $collection = $this->search($search, $searcBy)->get()
                ->sortBy(function ($item) use ($arrayIntersectUuidsIn, $arrayIntersectUuidsNotIn) {
                    if (in_array($item->uuid, $arrayIntersectUuidsIn) || !in_array($item->uuid, $arrayIntersectUuidsNotIn)) {

                        return $item->created_at;
                    }
                }, SORT_STRING, true);

            return $this->collectionPagination($collection, $perPage);
        } elseif (!empty($uuidsIn) && !empty($arrayIntersectUuidsIn) && empty($uuidsNotIn) && empty($arrayIntersectUuidsNotIn)) {
            //Uuids_in
            $collection = $this->search($search, $searcBy)->get()
                ->sortBy(function ($item) use ($arrayIntersectUuidsIn) {
                    if (in_array($item->uuid, $arrayIntersectUuidsIn)) {

                        return $item->created_at;
                    }
                }, SORT_STRING, true);

            return $this->collectionPagination($collection, $perPage);
        } elseif (!empty($uuidsNotIn) && !empty($arrayIntersectUuidsNotIn) && empty($uuidsIn) && empty($arrayIntersectUuidsIn)) {
            //Uuids_Not_in
            $collection = $this->search($search, $searcBy)->get()
                ->sortBy(function ($item) use ($arrayIntersectUuidsNotIn) {
                    if (!in_array($item->uuid, $arrayIntersectUuidsNotIn)) {

                        return $item->created_at;
                    }
                }, SORT_STRING, true);

            return $this->collectionPagination($collection, $perPage);
        } elseif (
            (!empty($uuidsIn) && !empty($uuidsNotIn) && empty($arrayIntersectUuidsIn) && empty($arrayIntersectUuidsNotIn)) ||
            (!empty($uuidsIn) && !empty($uuidsNotIn) && !empty($arrayIntersectUuidsIn) && empty($arrayIntersectUuidsNotIn)) ||
            (!empty($uuidsIn) && !empty($uuidsNotIn) && empty($arrayIntersectUuidsIn) && !empty($arrayIntersectUuidsNotIn))
        ) {

            return $this->collectionPagination([], $perPage);
        }

        return $this->search($search, $searcBy);
    }

    /**
     * @param $results
     * @param $perPage
     * @param $page
     * @return LengthAwarePaginator
     */
    public function collectionPagination($results, $perPage, $page = null)
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        $results = $results instanceof Collection ? $results : Collection::make($results);

        return new LengthAwarePaginator($results->forPage($page, $perPage)->values(), $results->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }
}
