<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\MyCampaignQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MyCampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    protected $modelQueryBuilderClass = MyCampaignQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyCampaignByKeyOrAbort($id)
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
    public function deleteMyCampaignByKey($id)
    {
        $campaign = $this->findMyCampaignByKeyOrAbort($id);

        return $this->destroy($campaign->getKey());
    }

    /**
     * @param $column
     * @param $id
     * @return bool
     */
    public function checkActiveMyCampainByColumn($column, $id)
    {
        if ($column === "type"){
            $activeCampaign = $this->model->where([
                ['uuid', $id],
                ['user_uuid', auth()->user()->getKey()]
            ])->whereIn('type', ['simple', 'scenario'])->first();
        }else {
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
                ['uuid', $id],
                ['user_uuid', auth()->user()->getKey()],
                $query
            ])->first();

        }

        if (!empty($activeCampaign)) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function CheckMyCampaign($campaignUuid)
    {
        $myCampaign = $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where([
                ['campaigns.uuid', $campaignUuid],
                ['websites.user_uuid', auth()->user()->getKey()],
                ['campaigns.from_date', '<=', Carbon::now()],
                ['campaigns.to_date', '>=', Carbon::now()],
                ['campaigns.was_finished', false],
                ['campaigns.was_stopped_by_owner', false],
            ])->first();

        if($myCampaign){
            return true;
        }
        return false;
    }

    /**
     * @param $model
     * @return array
     */
    public function findContactListKeyByMyCampaign($model)
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
    public function getMyCampaignChart($startDate, $endDate, $groupBy)
    {
        $times = $result = $chartResult = [];
        $check = true;
        $subDate = $startDate;
        $startDate = Carbon::parse($startDate);

        if($groupBy === "hour"){
            $dateFormat = "%Y-%m-%d %H:00:00";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d H:00:00');
                $startDate = $startDate->addHour();
            }
        }

        if($groupBy === "date"){
            $dateFormat = "%Y-%m-%d";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d');
                $startDate = $startDate->addDay();
            }
        }

        if($groupBy === "month"){
            $dateFormat = "%Y-%m";
            $subDate = Carbon::parse($subDate)->subMonth();
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m');
                $startDate = $startDate->addMonth();
            }
        }

        $campaignsChart = $this->createQueryGetCampaignChart($dateFormat, $subDate, $endDate);
        $campaignsIncrease = $this->createQueryGetIncrease($dateFormat, $subDate, $endDate, $groupBy === 'date' ? 'day' : $groupBy);

        if(!empty($campaignsChart)){
            foreach($campaignsChart as $campaignChart){
                foreach($campaignsIncrease as $campaignIncrease){
                    if(in_array($campaignIncrease->date_field, $campaignChart)){
                        $chartResult[] = array_merge($campaignChart, [
                            'increase' => $campaignIncrease->increase
                        ]);
                    }
                }
            }
        }

        $lastIncrease = 0;
        foreach ($times as $time){
            if(!empty($chartResult)){
                foreach ($chartResult as $chartItem){
                    if(in_array($time, $chartItem)){
                        $result[] = [
                            'label' => $time,
                            'active' => $chartItem['active'],
                            'other' => $chartItem['other'],
                            'increase' => $chartItem['increase'] ?? $chartItem['active'] + $chartItem['other']
                        ];
                        $lastIncrease = $chartItem['active'] + $chartItem['other'];
                        $check = true;
                        break;
                    }else{
                        $prevTime = $time;
                        if($groupBy === 'hour'){
                            $prevTime = Carbon::parse($prevTime)->subHour()->toDateTimeString();
                        }
                        if($groupBy === 'date'){
                            $prevTime = Carbon::parse($prevTime)->subDay()->toDateString();
                        }
                        if($groupBy === 'month'){
                            $prevTime = Carbon::parse($prevTime)->subMonth()->format('Y-m');
                        }
                        if(in_array($prevTime, $chartItem)){
                            $lastIncrease = $chartItem['active'] + $chartItem['other'];
                        }
                        $check = false;
                    }
                }

                if(!$check){
                    $result[] = [
                        'label' => $time,
                        'active' => 0,
                        'other' => 0,
                        'increase' => -$lastIncrease
                    ];
                    $lastIncrease = 0;
                }
            }else{
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
    public function createQueryGetCampaignChart($dateFormat, $startDate, $endDate){
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,  COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->where('user_uuid', auth()->user()->getKey())
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
                  where date(updated_at) >= '{$startDate}' and date(updated_at) <= '{$endDate}' and user_uuid = ".auth()->user()->getKey()." and deleted_at is NULL
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
    public function getTotalActiveAndOtherMyCampaignChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->where('user_uuid', auth()->user()->getKey())->get()->toArray();
    }
}
