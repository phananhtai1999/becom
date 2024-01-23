<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\CreditHistoryQueryBuilder;
use App\Models\CreditHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditHistoryService extends AbstractService
{
    protected $modelClass = CreditHistory::class;

    protected $modelQueryBuilderClass = CreditHistoryQueryBuilder::class;

    /**
     * @param $startDate
     * @param $endDate
     * @return bool|int
     */
    public function totalCreditUsed($startDate, $endDate)
    {
        $totalCreditUsed = $this->model->selectRaw('SUM(credit) as sum')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        return !empty($totalCreditUsed['0']->sum) ? $totalCreditUsed['0']->sum : 0;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateTime
     * @return array
     */
    public function queryUserUseCreditHistory($startDate, $endDate, $dateTime)
    {
        return $this->model->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, '0' as 'add', SUM(credit) as used ")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
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
    public function queryUserCreditHistory($startDate, $endDate, $dateTime)
    {
        return $this->model->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, SUM(credit) as 'add', '0' as used")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
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
    public function creditChart($groupBy, $startDate, $endDate)
    {
        $parseStartDate = Carbon::parse($startDate);
        $dateTime = [];
        $data = [];
        $result = [];

        if ($groupBy === 'hour') {
            $userCreditHistories = $this->queryUserCreditHistory($startDate, $endDate, "%Y-%m-%d %H:00:00");
            $userUseCreditHistories = $this->queryUserUseCreditHistory($startDate, $endDate, "%Y-%m-%d %H:00:00");
            $parseEndDate = Carbon::parse($endDate)->endOfDay();
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d H:00:00'),
                ];
                $parseStartDate->addHour();
            }
        } elseif ($groupBy === 'date') {
            $userCreditHistories = $this->queryUserCreditHistory($startDate, $endDate, "%Y-%m-%d");
            $userUseCreditHistories = $this->queryUserUseCreditHistory($startDate, $endDate, "%Y-%m-%d");
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d'),
                ];
                $parseStartDate->addDay();
            }
        } elseif ($groupBy === 'month') {
            $userCreditHistories = $this->queryUserCreditHistory($startDate, $endDate, "%Y-%m");
            $userUseCreditHistories = $this->queryUserUseCreditHistory($startDate, $endDate, "%Y-%m");
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
                    if (in_array($userCreditHistory['label'], $value)) {
                        $check = true;
                        $data [] = [
                            'label' => $userCreditHistory['label'],
                            'add' => $userCreditHistory['add'],
                            'used' => $userCreditHistory['used'],
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
                    if (in_array($userUseCreditHistory['label'], [$item['label']])) {
                        $check = true;
                        $result [] = [
                            'label' => $item['label'],
                            'add' => (int)$item['add'],
                            'used' => (int)$userUseCreditHistory['used'],
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
