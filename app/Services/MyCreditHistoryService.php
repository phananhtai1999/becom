<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyCreditHistoryQueryBuilder;
use App\Models\CreditHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyCreditHistoryService extends AbstractService
{
    protected $modelClass = CreditHistory::class;

    protected $modelQueryBuilderClass = MyCreditHistoryQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyCreditHistory($id)
    {
        return  $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyCreditHistory($id)
    {
        $credit_history = $this->showMyCreditHistory($id);

        return $this->destroy($credit_history->getKey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public function useMyCreditHistories()
    {
        return QueryBuilder::for($this->model)
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::exact('uuid'),
                AllowedFilter::exact('user_uuid'),
                AllowedFilter::exact('credit'),
                AllowedFilter::exact('campaign_uuid'),
                AllowedFilter::exact('add_by_uuid'),

            ])
            ->where('user_uuid', auth()->user()->getkey())
            ->select('uuid', 'user_uuid', 'credit', 'campaign_uuid', DB::raw('NULL as campaign_uuid'), 'created_at');
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public function myTotalCreditUsed($startDate, $endDate)
    {
        $totalMyCreditUsed = DB::table('user_use_credit_histories')->selectRaw('SUM(credit) as sum')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->where('user_uuid', auth()->user()->getkey())
            ->get();

        return !empty($totalMyCreditUsed['0']->sum) ? $totalMyCreditUsed['0']->sum : 0;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateTime
     * @return array
     */
    public function queryMyUseCreditHistory($startDate, $endDate, $dateTime)
    {
        return DB::table('user_use_credit_histories')->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, '0' as 'add', SUM(credit) as used ")
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
    public function queryMyAddCreditHistory($startDate, $endDate, $dateTime)
    {
        return DB::table('user_credit_histories')->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, SUM(credit) as 'add', '0' as used")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->where('user_uuid',auth()->user()->getkey())
            ->orderBy('label', 'ASC')
            ->groupby('label')
            ->get()->toArray();
    }

    /**
     * @param $groupBy
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function myCreditChart($groupBy, $startDate, $endDate)
    {
        $parseStartDate = Carbon::parse($startDate);
        $dateTime = [];
        $data = [];
        $result = [];

        if ($groupBy === 'hour') {
            $userCreditHistories = $this->queryMyAddCreditHistory($startDate, $endDate, "%Y-%m-%d %H:00:00");
            $userUseCreditHistories = $this->queryMyUseCreditHistory($startDate, $endDate, "%Y-%m-%d %H:00:00");
            $parseEndDate = Carbon::parse($endDate)->endOfDay();
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d H:00:00'),
                ];
                $parseStartDate->addHour();
            }
        } elseif ($groupBy === 'date') {
            $userCreditHistories = $this->queryMyAddCreditHistory($startDate, $endDate, "%Y-%m-%d");
            $userUseCreditHistories = $this->queryMyUseCreditHistory($startDate, $endDate, "%Y-%m-%d");
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d'),
                ];
                $parseStartDate->addDay();
            }
        } elseif ($groupBy === 'month') {
            $userCreditHistories = $this->queryMyAddCreditHistory($startDate, $endDate, "%Y-%m");
            $userUseCreditHistories = $this->queryMyUseCreditHistory($startDate, $endDate, "%Y-%m");
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m'),
                ];
                $parseStartDate->addMonth();
            }
        }

        foreach ($dateTime as $value) {
            if (!empty($userCreditHistories)) {
                foreach ($userCreditHistories as $userCreditHistory) {
                    if (in_array($userCreditHistory->label, $value)) {
                        $check = true;
                        $data [] = [
                            'label' => $userCreditHistory->label,
                            'add' => $userCreditHistory->add,
                            'used' => $userCreditHistory->used,
                        ];
                        break;
                    } else {
                        $check = false;
                    }
                }
                if (!($check)) {
                    $data [] = [
                        'label' => $value['date_time'],
                        'add' => 0,
                        'used' => 0,
                    ];
                }
            } else {
                $data [] = [
                    'label' => $value['date_time'],
                    'add' => 0,
                    'used' => 0,
                ];
            }
        }

        foreach ($data as $item) {
            if (!empty($userUseCreditHistories)) {
                foreach ($userUseCreditHistories as $userUseCreditHistory) {
                    if (in_array($userUseCreditHistory->label, [$item['label']])) {
                        $check = true;
                        $result [] = [
                            'label' => $item['label'],
                            'add' => (int)$item['add'],
                            'used' => (int)$userUseCreditHistory->used,
                        ];
                        break;
                    } else {
                        $check = false;
                    }
                }
                if (!($check)) {
                    $result [] = [
                        'label' => $item['label'],
                        'add' => (int)$item['add'],
                        'used' => (int)$item['used'],
                    ];
                }
            } else {
                $result [] = [
                    'label' => $item['label'],
                    'add' => (int)$item['add'],
                    'used' => (int)$item['used'],
                ];
            }
        }

        return $result;
    }
}
