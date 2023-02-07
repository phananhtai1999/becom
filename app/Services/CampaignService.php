<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\SortTotalCreditOfCampaignQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
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
                ['campaigns.from_date', '<=', Carbon::now('Asia/Ho_Chi_Minh')],
                ['campaigns.to_date', '>=', Carbon::now('Asia/Ho_Chi_Minh')],
                ['campaigns.was_finished', false],
                ['campaigns.was_stopped_by_owner', false],
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
                $query = [$column, '<=', Carbon::now('Asia/Ho_Chi_Minh')];
            }
            if ($column === "to_date") {
                $query = [$column, '>=', Carbon::now('Asia/Ho_Chi_Minh')];
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

        $times = $result = [];
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

        $campaignsIncrease = $this->createQueryGetIncrease($dateFormat, $subDate, $endDate, $groupBy === 'date' ? 'day' : $groupBy);

        foreach ($times as $time) {
            if (!empty($campaignsIncrease)) {
                $campaignsIncreaseByLabel = $campaignsIncrease->keyBy('label');
                $campaignIncrease = $campaignsIncreaseByLabel->first(function ($value, $key) use ($time) {
                    return $key === $time;
                });
                if ($campaignIncrease) {
                    $result[] = [
                        'label' => $time,
                        'active' => $campaignIncrease->active,
                        'other' => $campaignIncrease->other,
                        'increase' => $campaignIncrease->increase ?? $campaignIncrease->active + $campaignIncrease->other
                    ];
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
                    $campaignPrevTime = $campaignsIncreaseByLabel->first(function ($value, $key) use ($prevTime) {
                        return $key === $prevTime;
                    });
                    $result[] = [
                        'label' => $time,
                        'active' => 0,
                        'other' => 0,
                        'increase' => -(!$campaignPrevTime ? 0 : $campaignPrevTime->active + $campaignPrevTime->other)
                    ];
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
     * @param $type
     * @return \Illuminate\Support\Collection
     */
    public function createQueryGetIncrease($dateFormat, $startDate, $endDate, $type)
    {

        /*
         SELECT A.date_field, A.active, A.other, B.increase
        FROM
            (select date_format(updated_at, '%Y-%m-%d') as date_field,  COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other
            from `campaigns`
            group by `date_field` order by `date_field` asc) as A
        JOIN (SELECT today.date_field, today.createCampaign, (today.createCampaign - yest.createCampaign) as increase
            FROM (SELECT date_format(updated_at, '%Y-%m-%d') as date_field, COUNT(uuid) as createCampaign
            from campaigns
            GROUP By date_field) today LEFT JOIN
            (SELECT date_format(updated_at, '%Y-%m-%d') as date_field, COUNT(uuid) as createCampaign
            from campaigns
            GROUP By date_field) yest On yest.date_field = today.date_field - INTERVAL 1 day) as B on A.date_field = B.date_field;
        */
        $string = $type === "month" ? "-01" : "";
        $todayCampaignTableSubQuery = $yesterdayCampaignTableSubQuery = $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label, COUNT(uuid) as createCampaign")
            ->whereRaw('date(updated_at) >= "' . $startDate . '"')
            ->whereRaw('date(updated_at) <= "' . $endDate . '"')->groupBy('label')->toSql();
        $campaignStatusTable = $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,  COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other")
            ->whereRaw('date(updated_at) >= "' . $startDate . '"')
            ->whereRaw('date(updated_at) <= "' . $endDate . '"')
            ->groupBy('label')
            ->orderBy('label', 'ASC')->toSql();

        $campaignIncreaseTable = DB::table(DB::raw("($todayCampaignTableSubQuery) as today"))->selectRaw("today.label, today.createCampaign, (today.createCampaign - yest.createCampaign) as increase")
            ->leftJoin(DB::raw("($yesterdayCampaignTableSubQuery) as yest"), 'yest.label', '=', DB::raw("date_format(concat(today.label, '$string') - INTERVAL 1 {$type}, '{$dateFormat}')"))
            ->toSql();
        return DB::table(DB::raw("($campaignStatusTable) as campaignStatusTable"))->selectRaw("campaignStatusTable.label, campaignStatusTable.active, campaignStatusTable.other, campaignIncreaseTable.increase")
            ->join(DB::raw("($campaignIncreaseTable) as campaignIncreaseTable"), 'campaignStatusTable.label', '=', 'campaignIncreaseTable.label')
            ->get();

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
            ->whereDate('updated_at', '<=', $endDate)->first()->setAppends([]);
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
            ['to_date', '>=', Carbon::now('Asia/Ho_Chi_Minh')],
            ['was_finished', false],
            ['was_stopped_by_owner', false],
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

    /**
     * @param $perPage
     * @param $sort
     * @return LengthAwarePaginator
     */
    public function sortTotalCredit($perPage, $sort)
    {
        if ($sort == 'number_credit_needed_to_start_campaign') {
            $sortTotalCredit = SortTotalCreditOfCampaignQueryBuilder::initialQuery()->get()->sortBy('number_credit_needed_to_start_campaign');
        } elseif ($sort == '-number_credit_needed_to_start_campaign') {
            $sortTotalCredit = SortTotalCreditOfCampaignQueryBuilder::initialQuery()->get()->sortByDesc('number_credit_needed_to_start_campaign');
        }

        return $this->collectionPagination($sortTotalCredit, $perPage);
    }
}
