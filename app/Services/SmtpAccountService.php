<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Mail\SendEmails;
use App\Models\QueryBuilders\SmtpAccountQueryBuilder;
use App\Models\SmtpAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Swift_SmtpTransport;
use Swift_Mailer;
use Illuminate\Support\Facades\Mail;

class SmtpAccountService extends AbstractService
{
    protected $modelClass = SmtpAccount::class;

    protected $modelQueryBuilderClass = SmtpAccountQueryBuilder::class;

    /**
     * @param $id
     * @return bool
     */
    public function checkExistsSmtpAccountInTables($id)
    {
        $smtpAccount = $this->findOrFailById($id);

        $campaigns = $smtpAccount->campaigns->toArray();

        if (!empty($campaigns)) {
            return true;
        }

        return false;
    }

    /**
     * @param $mailUserName
     * @param $userUuid
     * @return bool
     */
    public function checkMailUserNameUnique($mailUserName, $userUuid)
    {
        $smtpAccount = $this->findOneWhere([
            ['user_uuid', $userUuid],
            ['mail_username', $mailUserName]
        ]);
        if ($smtpAccount) {
            return false;
        }

        return true;
    }

    /**
     * @param $smtpAccount
     * @return void
     */
    public function setSmtpAccountForSendEmail($smtpAccount)
    {
        Config::set('mail.mailers.smtp.transport', $smtpAccount['mail_mailer'] ?? 'smtp');
        Config::set('mail.mailers.smtp.host', $smtpAccount['mail_host']);
        Config::set('mail.mailers.smtp.port', $smtpAccount['mail_port']);
        Config::set('mail.mailers.smtp.username', $smtpAccount['mail_username']);
        Config::set('mail.mailers.smtp.password', $smtpAccount['mail_password']);
        Config::set('mail.mailers.smtp.encryption', $smtpAccount->smtpAccountEncryption->name ?? $smtpAccount['mail_encryption']);
        Config::set('mail.from.address', $smtpAccount['mail_from_address']);
        Config::set('mail.from.name', $smtpAccount['mail_from_name']);
    }

    /**
     * @param $smtpAccount
     * @return void
     */
    public function setSwiftSmtpAccountForSendEmail($smtpAccount) //Thiết lập đổi smtp account khi gửi email bằng queue
    {
        $transport = new Swift_SmtpTransport($smtpAccount->mail_host, $smtpAccount->mail_port, $smtpAccount->smtpAccountEncryption->name);
        $transport->setUsername($smtpAccount->mail_username);
        $transport->setPassword($smtpAccount->mail_password);
        $mailtrap = new Swift_Mailer($transport);
        Mail::setSwiftMailer($mailtrap);
    }

    /**
     * @param $sendTypeCampaign
     * @return mixed
     */
    public function getRandomSmtpAccountAdmin($sendTypeCampaign)
    {
        return $this->model->select('smtp_accounts.*')
            ->join('users', 'users.uuid', '=', 'smtp_accounts.user_uuid')
            ->join('role_user', 'role_user.user_uuid', '=', 'users.uuid')
            ->where(function ($query) use ($sendTypeCampaign) {
                if ($sendTypeCampaign == 'email') {

                    return $query->where([
                        ['role_user.role_uuid', config('user.default_admin_role_uuid')],
                        ['smtp_accounts.mail_mailer', 'smtp'],
                        ['smtp_accounts.status', 'work'],
                        ['smtp_accounts.publish', true],
                        ['smtp_accounts.website_uuid', null]
                    ]);
                } else {

                    return $query->where([
                        ['role_user.role_uuid', config('user.default_admin_role_uuid')],
                        ['smtp_accounts.mail_mailer', $sendTypeCampaign],
                        ['smtp_accounts.status', 'work'],
                        ['smtp_accounts.publish', true],
                        ['smtp_accounts.website_uuid', null]
                    ]);
                }
            })->inRandomOrder()->first();
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

    /**
     * @param $smtpAccount
     * @return bool
     */
    public function testSmtpAccount($smtpAccount)
    {
        if ($smtpAccount->mail_mailer === 'smtp') {
            try {
                $subject = "Test SMTP ACCOUNT";
                $body = "Test SMTP ACCOUNT";

                $this->setSmtpAccountForSendEmail($smtpAccount);

                Mail::to(config('user.email_test'))->send(new SendEmails($subject, $body));

                return true;
            }catch (\Exception $e){
                return false;
            }
        }
        return true;
    }

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSmtpAccountDefaultWithPagination($perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        return SmtpAccountQueryBuilder::searchQuery($search, $searchBy)
            ->whereNull('website_uuid')
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function getAllSmtpAccountWithoutDefault($perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        return SmtpAccountQueryBuilder::searchQuery($search, $searchBy)
            ->whereNotNull('website_uuid')
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
