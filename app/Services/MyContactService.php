<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Contact;
use App\Models\QueryBuilders\MyContactQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
}
