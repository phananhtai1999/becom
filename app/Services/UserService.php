<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserQueryBuilder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserService extends AbstractService
{
    protected $modelClass = User::class;

    protected $modelQueryBuilderClass = UserQueryBuilder::class;

    /**
     * @return User|null
     */
    public function currentUser(): ?User
    {
        $userUUID = app(UserAccessTokenService::class)->getCurrentUserKey();

        if ($userUUID) {
            return $this->findOneById($userUUID);
        }

        return null;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function showByUserName($key)
    {
        return $this->model->where('username', $key)->firstOrFail();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function findByEmail($key)
    {
        return $this->model->where('email', $key)->first();
    }

    /**
     * @param $email
     * @return mixed
     */
    public function findUserLogin($email)
    {
        return $this->model->withTrashed()->where([
            'email' => $email
        ])->orderBy('uuid', 'DESC')->first();
    }

    /**
     * @param $creditNumber
     * @param $userUuid
     * @return bool
     */
    public function checkCredit($creditNumber, $userUuid)
    {
        $user = $this->findOneById($userUuid);
        if ($user->credit < $creditNumber) {
            return false;
        }
        return true;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Support\Collection
     */
    public function totalBannedAndActive($startDate, $endDate)
    {
        return DB::table('users')->selectRaw("count(uuid) - count(banned_at) as active, count(banned_at) as banned")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateTime
     * @return array
     */
    public function queryUser($startDate, $endDate, $dateTime)
    {
        return DB::table('users')->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, count(uuid) - count(banned_at) as active, count(banned_at) as banned")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
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
        $string = $type === "month" ? "-01" : "";
        $todayUserTableSubQuery = $yesterdayUserTableSubQuery = "(SELECT date_format(created_at, '{$dateFormat}') as date_field, COUNT(uuid) as createUser
                  from users
                  where date(created_at) >= '{$startDate}' and date(created_at) <= '{$endDate}' and deleted_at is NULL
                  GROUP By date_field)";

        return DB::table(DB::raw("$todayUserTableSubQuery as today"))->selectRaw("today.date_field, today.createUser, (today.createUser - yest.createUser) as increase")
            ->leftJoin(DB::raw("$yesterdayUserTableSubQuery as yest"), 'yest.date_field', '=', DB::raw("date_format(concat(today.date_field, '$string') - INTERVAL 1 {$type}, '{$dateFormat}')"))
            ->get()->toArray();
    }

    /**
     * @param $groupBy
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function userChart($groupBy, $startDate, $endDate)
    {
        $parseStartDate = Carbon::parse($startDate);
        $dateTime = $chartResult = [];
        $result = [];

        if ($groupBy === 'hour') {
            $dateFormat = "%Y-%m-%d %H:00:00";
            $subDate = Carbon::parse($startDate)->subDay();
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
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m'),
                ];
                $parseStartDate->addMonth();
            }
        }

        $users = $this->queryUser($subDate, $parseEndDate, $dateFormat);
        $usersIncrease = $this->createQueryGetIncrease($subDate, $endDate, $dateFormat, $groupBy === 'date' ? 'day' : $groupBy);
        if (!empty($users)) {
            foreach ($users as $user) {
                foreach ($usersIncrease as $userIncrease) {
                    if (in_array($userIncrease->date_field, [$user->label])) {
                        $chartResult[] = [
                            'label' => $user->label,
                            'active' => $user->active,
                            'banned' => $user->banned,
                            'increase' => $userIncrease->increase
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
                        $result[] = [
                            'label' => $value['date_time'],
                            'active' => $chartItem['active'],
                            'banned' => $chartItem['banned'],
                            'increase' => $chartItem['increase'] ?? $chartItem['active'] + $chartItem['banned']
                        ];
                        $lastIncrease = $chartItem['active'] + $chartItem['banned'];
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
                            $lastIncrease = $chartItem['active'] + $chartItem['banned'];
                        }
                        $check = false;
                    }
                }
                if (!($check)) {
                    $result[] = [
                        'label' => $value['date_time'],
                        'active' => 0,
                        'banned' => 0,
                        'increase' => -$lastIncrease
                    ];
                    $lastIncrease = 0;
                }
            } else {
                $result [] = [
                    'label' => $value['date_time'],
                    'active' => 0,
                    'banned' => 0,
                    'increase' => 0
                ];
            }
        }

        return $result;
    }
}
