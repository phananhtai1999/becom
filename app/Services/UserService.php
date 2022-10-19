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
        ])->first();
    }

    /**
     * @param $contactsNumberSendEmail
     * @param $userUuid
     * @return bool
     */
    public function checkCreditToSendCEmail($creditNumberSendEmail, $userUuid)
    {
        $user = $this->findOneById($userUuid);
        if ($user->credit < $creditNumberSendEmail) {
            return false;
        }
        return true;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function totalUserActives($startDate, $endDate)
    {
        return $this->model->where([
            ['deleted_at', null],
            ['banned_at', null]
        ])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get()->count();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function totalUserBanned($startDate, $endDate)
    {
        return $this->model->where([
            ['deleted_at', null],
            ['banned_at', '<>', null]
        ])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get()->count();
    }

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

    public function userTrackingChart($groupBy, $startDate, $endDate)
    {
        $parseStartDate = Carbon::parse($startDate);
        $dateTime = [];
        $result = [];

        if ($groupBy === 'hour') {
            $users = $this->queryUser($startDate, $endDate, "%Y-%m-%d %H:00:00");
            $parseEndDate = Carbon::parse($endDate)->endOfDay();
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d H:00:00'),
                ];
                $parseStartDate->addHour();
            }
        } elseif ($groupBy === 'date') {
            $users = $this->queryUser($startDate, $endDate, "%Y-%m-%d");
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m-d'),
                ];
                $parseStartDate->addDay();
            }
        } elseif ($groupBy === 'month') {
            $users = $this->queryUser($startDate, $endDate, "%Y-%m");
            $parseEndDate = Carbon::parse($endDate);
            while ($parseStartDate <= $parseEndDate) {
                $dateTime[] = [
                    'date_time' => $parseStartDate->format('Y-m'),
                ];
                $parseStartDate->addMonth();
            }
        }
        foreach ($dateTime as $value) {
            if (!empty($users)) {
                foreach ($users as $user) {
                    if (in_array($user->label, $value)) {
                        $check = true;
                        $result [] = $user;
                        break;
                    } else {
                        $check = false;
                    }
                }
                if (!($check)) {
                    $result [] = [
                        'label' => $value['date_time'],
                        'active' => 0,
                        'banned' => 0,
                    ];
                }
            } else {
                $result [] = [
                    'label' => $value['date_time'],
                    'active' => 0,
                    'banned' => 0,
                ];
            }
        }

        return $result;
    }
}
