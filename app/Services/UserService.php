<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserQueryBuilder;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    /**
     * @return bool
     */
    public function checkLanguagesPermission()
    {
        if (auth()->guest()) {
            return false;
        }

        return auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN, Role::ROLE_EDITOR]);
    }

    /**
     * @return false
     */
    public function checkLanguagesPermissionWithAdminAndRootRole()
    {
        if (auth()->guest()) {
            return false;
        }

        return auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN]);
    }

    public function getUsersByRole($role)
    {
        $users = $this->model->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->with('roles')->get();

        if ($role === "user") {
            $users = $users->filter(function ($user) {
                return $user->roles->count() === 1;
            })->values();
        }

        return $users;
    }

    public function getMinCodeByNumberOfUser()
    {
        $min = $power = 6;
        $lastUser = optional($this->model->orderBy('uuid', 'DESC')->first())->uuid;
        $nextPower = pow(10, $power + 1);
        while ($lastUser >= $nextPower) {
            $min++;
            $power++;
            $nextPower = pow(10, $power + 1);
        }

        return $min;
    }

    /**
     * @return string
     */
    public function getCurrentUserRole(): string
    {
        if (auth()->hasRole([Role::ROLE_ROOT])) {
            $char = 'r' . auth()->userId();
        } elseif (auth()->hasRole([Role::ROLE_ADMIN])) {
            $char = 'a' . auth()->userId();
        } elseif (auth()->hasRole([Role::ROLE_EDITOR])) {
            $char = 'e' . auth()->userId();
        } else {
            $char = 'u' . auth()->userId();
        }

        return $char;
    }

    public function getUsersOfAdmin($request)
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereDoesntHave('roles', function (Builder $query) {
                $query->where('name', 'root');
            })->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function showUserOfAdminById($id)
    {
        return $this->model->whereDoesntHave('roles', function (Builder $query) {
            $query->where('name', 'root');
        })->where('uuid', $id)->firstOrFail();
    }

    public function createUserByRequest($request)
    {
        if (empty($request->can_add_smtp_account)) {
            $model = $this->create(array_merge($request->all(), [
                'password' => Hash::make($request->get('password')),
                'can_add_smtp_account' => '0'
            ]));
        } else {
            $model = $this->create(array_merge($request->all(), [
                'password' => Hash::make($request->get('password')),
            ]));
        }

        $model->roles()->sync(
            array_merge($request->get('roles', []), [config('user.default_role_uuid')])
        );

        return $model;
    }

    public function deleteUserOfAdminById($id)
    {
        $user = $this->showUserOfAdminById($id);

        return $user->delete();
    }
}
