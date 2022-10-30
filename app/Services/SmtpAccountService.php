<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SmtpAccountQueryBuilder;
use App\Models\SmtpAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SmtpAccountService extends AbstractService
{
    protected $modelClass = SmtpAccount::class;

    protected $modelQueryBuilderClass = SmtpAccountQueryBuilder::class;

    /**
     * @param $smtpAccount
     * @return void
     */
    public function setSmtpAccountForSendEmail($smtpAccount)
    {
        Config::set('mail.mailers.smtp.transport', $smtpAccount->mail_mailer);
        Config::set('mail.mailers.smtp.host', $smtpAccount->mail_host);
        Config::set('mail.mailers.smtp.port', $smtpAccount->mail_port);
        Config::set('mail.mailers.smtp.username', $smtpAccount->mail_username);
        Config::set('mail.mailers.smtp.password', $smtpAccount->mail_password);
        Config::set('mail.mailers.smtp.encryption', $smtpAccount->smtpAccountEncryption->name);
        Config::set('mail.from.address', $smtpAccount->mail_from_address);
        Config::set('mail.from.name', $smtpAccount->mail_from_name);
    }

    /**
     * @return mixed
     */
    public function getRandomSmtpAccountAdmin()
    {
        return $this->model->select('smtp_accounts.*')
            ->join('users', 'users.uuid', '=', 'smtp_accounts.user_uuid')
            ->join('role_user', 'role_user.user_uuid', '=', 'users.uuid')
            ->where('role_user.role_uuid', config('user.default_admin_role_uuid'))->inRandomOrder()->first();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Support\Collection
     */
    public function totalActiveAndInactiveSmtpAccountChart($startDate, $endDate)
    {
        return DB::table('smtp_accounts')->selectRaw("count(uuid) - count(deleted_at) as active, count(deleted_at) as inactive")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $dateTime
     * @return array
     */
    public function querySmtpAccount($startDate, $endDate, $dateTime)
    {
        return DB::table('smtp_accounts')->selectRaw("DATE_FORMAT(created_at, '{$dateTime}') as label, count(uuid) - count(deleted_at) as active, count(deleted_at) as inactive")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
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
        $todaySmtpAccountTableSubQuery = $yesterdaySmtpAccountTableSubQuery = "(SELECT date_format(created_at, '{$dateFormat}') as date_field, COUNT(uuid) as createSmtpAccount
                  from smtp_accounts
                  where date(created_at) >= '{$startDate}' and date(created_at) <= '{$endDate}' and deleted_at is NULL
                  GROUP By date_field)";

        return DB::table(DB::raw("$todaySmtpAccountTableSubQuery as today"))->selectRaw("today.date_field, today.createSmtpAccount, (today.createSmtpAccount - yest.createSmtpAccount) as increase")
            ->leftJoin(DB::raw("$yesterdaySmtpAccountTableSubQuery as yest"), 'yest.date_field', '=', DB::raw("date_format(concat(today.date_field, '$string') - INTERVAL 1 {$type}, '{$dateFormat}')"))
            ->get()->toArray();
    }

    /**
     * @param $groupBy
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function smtpAccountChart($groupBy, $startDate, $endDate)
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

        $smtpAccounts = $this->querySmtpAccount($subDate, $parseEndDate, $dateFormat);
        $smtpAccountsIncrease = $this->createQueryGetIncrease($subDate, $endDate, $dateFormat, $groupBy === 'date' ? 'day' : $groupBy);
        if (!empty($smtpAccounts)) {
            foreach ($smtpAccounts as $smtpAccount) {
                foreach ($smtpAccountsIncrease as $smtpAccountIncrease) {
                    if (in_array($smtpAccountIncrease->date_field, [$smtpAccount->label])) {
                        $chartResult[] = [
                            'label' => $smtpAccount->label,
                            'active' => $smtpAccount->active,
                            'inactive' => $smtpAccount->inactive,
                            'increase' => $smtpAccountIncrease->increase
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
                            'inactive' => $chartItem['inactive'],
                            'increase' => $chartItem['increase'] ?? $chartItem['active'] + $chartItem['inactive']
                        ];
                        $lastIncrease = $chartItem['active'] + $chartItem['inactive'];
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
                            $lastIncrease = $chartItem['active'] + $chartItem['inactive'];
                        }
                        $check = false;
                    }
                }
                if (!($check)) {
                    $result[] = [
                        'label' => $value['date_time'],
                        'active' => 0,
                        'inactive' => 0,
                        'increase' => -$lastIncrease
                    ];
                    $lastIncrease = 0;
                }
            } else {
                $result [] = [
                    'label' => $value['date_time'],
                    'active' => 0,
                    'inactive' => 0,
                    'increase' => 0
                ];
            }
        }

        return $result;
    }
}
