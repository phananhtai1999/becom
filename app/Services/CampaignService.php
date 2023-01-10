<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    protected $modelQueryBuilderClass = CampaignQueryBuilder::class;

    /**
     * @return mixed|void
     */
    public function loadActiveCampaign()
    {
        return $this->model->select('campaigns.*')
            ->whereNotIn('uuid', function ($query) {
                $query->select('campaigns.uuid')
                    ->from('campaigns')
                    ->join('send_email_schedule_logs', 'send_email_schedule_logs.campaign_uuid', '=', 'campaigns.uuid')
                    ->where('send_email_schedule_logs.is_running', true);
            })->where([
                ['campaigns.from_date', '<=', Carbon::now()],
                ['campaigns.to_date', '>=', Carbon::now()],
                ['campaigns.was_finished', false],
                ['campaigns.was_stopped_by_owner', false],
                ['campaigns.send_type', "email"],
                ['campaigns.status', "active"],
            ])->whereIn('campaigns.type', ['simple', 'scenario'])->firstOrFail();
    }

    /**
     * @return mixed
     */
    public function getListActiveBirthdayCampaign()
    {
        return $this->model->selectRaw('DISTINCT campaigns.*')->with(['user', 'mailTemplate', 'website', 'smtpAccount'])
            ->join('campaign_contact_list', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->join('contact_lists', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('contact_contact_list', 'contact_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('contacts', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->where([
                ['campaigns.type', 'birthday'],
                ['campaigns.from_date', '<=', Carbon::now()],
                ['campaigns.to_date', '>=', Carbon::now()],
                ['campaigns.was_finished', false],
                ['campaigns.was_stopped_by_owner', false],
                ['campaigns.send_type', "email"],
                ['campaigns.status', "active"],
            ])
            ->whereDate('contacts.dob', Carbon::now())
            ->whereNull('contacts.deleted_at')->get();
    }

    /**
     * @param $column
     * @param $id
     * @return bool
     */
    public function checkActiveCampainByColumn($column, $id)
    {
        if ($column === "type") {
            $activeCampaign = $this->model->where([
                ['uuid', $id]
            ])->whereIn('type', ['simple', 'scenario'])->first();
        } else {
            $query = [];
            if ($column === "was_finished") {
                $query = [$column, false];
            }
            if ($column === "was_stopped_by_owner") {
                $query = [$column, false];
            }
            if ($column === "from_date") {
                $query = [$column, '<=', Carbon::now()];
            }
            if ($column === "to_date") {
                $query = [$column, '>=', Carbon::now()];
            }
            if ($column === "send_type") {
                $query = [$column, "email"];
            }
            if ($column === "status") {
                $query = [$column, "active"];
            }
            $activeCampaign = $this->model->where([
                ['uuid', $id], $query
            ])->first();

        }

        if (!empty($activeCampaign)) {
            return true;
        }

        return false;
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function findCampaignByReport($campaignUuid)
    {
        return $this->model->where('uuid', $campaignUuid)->withTrashed()->first();
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function findCampaignByCreditHistory($campaignUuid)
    {
        return $this->findOneById($campaignUuid);
    }

    /**
     * @param $model
     * @return array|void
     */
    public function findContactListKeyByCampaign($model)
    {
        $contactLists = $model->contactLists()->get();

        if (empty($contactLists)) {

            return [];
        } else {
            foreach ($contactLists as $contactList) {
                $contactListUuid[] = $contactList->uuid;

                return $contactListUuid;
            }
        }
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $groupBy
     * @return array
     */
    public function getCampaignChart($startDate, $endDate, $groupBy)
    {
        $times = $result = $chartResult = [];
        $check = true;
        $subDate = $startDate;
        $startDate = Carbon::parse($startDate);

        if ($groupBy === "hour") {
            $dateFormat = "%Y-%m-%d %H:00:00";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            while ($startDate <= $endDate) {
                $times[] = $startDate->format('Y-m-d H:00:00');
                $startDate = $startDate->addHour();
            }
        }

        if ($groupBy === "date") {
            $dateFormat = "%Y-%m-%d";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate);

            while ($startDate <= $endDate) {
                $times[] = $startDate->format('Y-m-d');
                $startDate = $startDate->addDay();
            }
        }

        if ($groupBy === "month") {
            $dateFormat = "%Y-%m";
            $subDate = Carbon::parse($subDate)->subMonth();
            $endDate = Carbon::parse($endDate);

            while ($startDate <= $endDate) {
                $times[] = $startDate->format('Y-m');
                $startDate = $startDate->addMonth();
            }
        }

        $campaignsChart = $this->createQueryGetCampaignChart($dateFormat, $subDate, $endDate);
        $campaignsIncrease = $this->createQueryGetIncrease($dateFormat, $subDate, $endDate, $groupBy === 'date' ? 'day' : $groupBy);

        if (!empty($campaignsChart)) {
            foreach ($campaignsChart as $campaignChart) {
                foreach ($campaignsIncrease as $campaignIncrease) {
                    if (in_array($campaignIncrease->date_field, $campaignChart)) {
                        $chartResult[] = array_merge($campaignChart, [
                            'increase' => $campaignIncrease->increase
                        ]);
                    }
                }
            }
        }

        $lastIncrease = 0;
        foreach ($times as $time) {
            if (!empty($chartResult)) {
                foreach ($chartResult as $chartItem) {
                    if (in_array($time, $chartItem)) {
                        $result[] = [
                            'label' => $time,
                            'active' => $chartItem['active'],
                            'other' => $chartItem['other'],
                            'increase' => $chartItem['increase'] ?? $chartItem['active'] + $chartItem['other']
                        ];
                        $lastIncrease = $chartItem['active'] + $chartItem['other'];
                        $check = true;
                        break;
                    } else {
                        $prevTime = $time;
                        if ($groupBy === 'hour') {
                            $prevTime = Carbon::parse($prevTime)->subHour()->toDateTimeString();
                        }
                        if ($groupBy === 'date') {
                            $prevTime = Carbon::parse($prevTime)->subDay()->toDateString();
                        }
                        if ($groupBy === 'month') {
                            $prevTime = Carbon::parse($prevTime)->subMonth()->format('Y-m');
                        }
                        if (in_array($prevTime, $chartItem)) {
                            $lastIncrease = $chartItem['active'] + $chartItem['other'];
                        }
                        $check = false;
                    }
                }

                if (!$check) {
                    $result[] = [
                        'label' => $time,
                        'active' => 0,
                        'other' => 0,
                        'increase' => -$lastIncrease
                    ];
                    $lastIncrease = 0;
                }
            } else {
                $result[] = [
                    'label' => $time,
                    'active' => 0,
                    'other' => 0,
                    'increase' => 0
                ];
            }
        }

        return $result;
    }

    /**
     * @param $dateFormat
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function createQueryGetCampaignChart($dateFormat, $startDate, $endDate)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,  COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get()->toArray();
    }

    /**
     * @param $dateFormat
     * @param $startDate
     * @param $endDate
     * @param $type
     * @return array
     */
    public function createQueryGetIncrease($dateFormat, $startDate, $endDate, $type)
    {
//                SELECT today.date_field, today.createCampaign, (today.createCampaign - yest.createCampaign) as increase
//         FROM (SELECT date_format(updated_at, '%Y-%m-%d') as date_field, COUNT(uuid) as createCampaign
//                  from campaigns
//                  where date(updated_at) >= '2022-10-03' AND date(updated_at) <= '2022-10-05'
//                  GROUP By date_field) today LEFT JOIN
//              (SELECT date_format(updated_at, '%Y-%m-%d') as date_field, COUNT(uuid) as createCampaign
//                  from campaigns
//                  where date(updated_at) >= '2022-10-03' AND date(updated_at) <= '2022-10-05'
//                  GROUP By date_field) yest On yest.date_field = today.date_field - INTERVAL 1 day;
        $string = $type === "month" ? "-01" : "";
        $todayCampaignTableSubQuery = $yesterdayCampaignTableSubQuery = "(SELECT date_format(updated_at, '{$dateFormat}') as date_field, COUNT(uuid) as createCampaign
                  from campaigns
                  where date(updated_at) >= '{$startDate}' and date(updated_at) <= '{$endDate}' and deleted_at is NULL
                  GROUP By date_field)";
        return DB::table(DB::raw("$todayCampaignTableSubQuery as today"))->selectRaw("today.date_field, today.createCampaign, (today.createCampaign - yest.createCampaign) as increase")
            ->leftJoin(DB::raw("$yesterdayCampaignTableSubQuery as yest"), 'yest.date_field', '=', DB::raw("date_format(concat(today.date_field, '$string') - INTERVAL 1 {$type}, '{$dateFormat}')"))
            ->get()->toArray();

    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getTotalActiveAndOtherCampaignChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)->get()->toArray();
    }

    /**
     * @param $campaign
     * @param $mailSendingHistory
     * @return bool
     */
    public function checkScenarioCampaign($campaign, $mailSendingHistory)
    {
        if (!empty($campaign->open_mail_campaign)) {
            if (empty($campaign->not_open_mail_campaign)) {
                return true;
            } else {
                $result = $this->model->select('campaigns.*')
                    ->join('mail_sending_history', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
                    ->where('mail_sending_history.uuid', $mailSendingHistory->uuid)
                    ->whereRaw('date(mail_sending_history.updated_at) - date(mail_sending_history.created_at) <= campaigns.open_within')
                    ->first();
                if (empty($result)) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function checkActiveCampaignScenario($campaignUuid)
    {
        return $this->model->where([
            ['uuid', $campaignUuid],
            ['to_date', '>=', Carbon::now()],
            ['was_finished', false],
            ['was_stopped_by_owner', false],
            ['send_type', "email"],
            ['status', "active"]
        ])->with(['user', 'mailTemplate', 'website', 'smtpAccount'])->first();

    }

    /**
     * @param $uuid
     * @param $fromDate
     * @param $toDate
     * @param $sendType
     * @param $type
     * @return float|int
     */
    public function numberOfCreditsToStartTheCampaign($uuid, $fromDate, $toDate, $sendType, $type)
    {
        if ($sendType == 'email') {
            $config = app(ConfigService::class)->findConfigByKey('email_price')->value;
        } elseif ($sendType == 'sms') {
            $config = app(ConfigService::class)->findConfigByKey('sms_price')->value;
        }

        if ($type == 'birthday') {
            $numberCreditNeededToSendCampaign = (app(ContactService::class)->getBirthdayContactsSendEmailsByCampaigns($uuid, $fromDate, $toDate)) * $config;
        } else {
            $numberCreditNeededToSendCampaign = (app(ContactService::class)->getListsContactsSendEmailsByCampaigns($uuid)) * $config;
        }

        return $numberCreditNeededToSendCampaign;
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getInfoRelationshipCampaignByUuid($campaignUuid)
    {
        return $this->model->with(['user', 'mailTemplate', 'website', 'smtpAccount'])->find($campaignUuid);
    }

}
