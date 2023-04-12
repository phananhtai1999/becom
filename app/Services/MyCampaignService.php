<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\MyCampaignQueryBuilder;
use App\Models\QueryBuilders\SortTotalCreditOfMyCampaignQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
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
                $query = [$column, '<=', Carbon::now('Asia/Ho_Chi_Minh')];
            }
            if ($column === "to_date") {
                $query = [$column, '>=', Carbon::now('Asia/Ho_Chi_Minh')];
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
                ['campaigns.from_date', '<=', Carbon::now('Asia/Ho_Chi_Minh')],
                ['campaigns.to_date', '>=', Carbon::now('Asia/Ho_Chi_Minh')],
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
        $times = $result = [];
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
                }else{
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
        $string = $type === "month" ? "-01" : "";
        $todayCampaignTableSubQuery = $yesterdayCampaignTableSubQuery = $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label, COUNT(uuid) as createCampaign")
            ->whereRaw('date(updated_at) >= "'.$startDate.'" and date(updated_at) <= "'.$endDate.'" and user_uuid = '.auth()->user()->getKey())
            ->groupBy('label')->toSql();
        $campaignStatusTable = $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,  COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other")
            ->whereRaw('date(updated_at) >= "'.$startDate.'" and date(updated_at) <= "'.$endDate.'" and user_uuid = '.auth()->user()->getKey())
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
    public function getTotalActiveAndOtherMyCampaignChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( status = 'active', 1, NULL ) ) as active, COUNT(IF( status <> 'active', 1, NULL ) ) as other")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->where('user_uuid', auth()->user()->getKey())->first()->setAppends([]);
    }

    /**
     * @param $perPage
     * @param $sort
     * @param $search
     * @param $searchBy
     * @return LengthAwarePaginator
     */
    public function sortMyTotalCredit($perPage, $sort, $search, $searchBy, $contactLists = [])
    {
        if (empty($contactLists)) {
            if ($sort == 'number_credit_needed_to_start_campaign') {
                $sortTotalCredit = SortTotalCreditOfMyCampaignQueryBuilder::searchQuery($search, $searchBy)
                    ->get()->sortBy('number_credit_needed_to_start_campaign');
            } elseif ($sort == '-number_credit_needed_to_start_campaign') {
                $sortTotalCredit = SortTotalCreditOfMyCampaignQueryBuilder::searchQuery($search, $searchBy)
                    ->get()->sortByDesc('number_credit_needed_to_start_campaign');
            }
        } else {
            $campaignUuids = $this->model->select('campaigns.*')
                ->join('campaign_contact_list', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
                ->WhereIn('campaign_contact_list.contact_list_uuid', $contactLists)->get()->pluck('uuid');

            if ($sort == 'number_credit_needed_to_start_campaign') {
                $sortTotalCredit = SortTotalCreditOfMyCampaignQueryBuilder::searchQuery($search, $searchBy)
                    ->OrWhereIn('uuid', $campaignUuids)
                    ->get()->sortBy('number_credit_needed_to_start_campaign');
            } elseif ($sort == '-number_credit_needed_to_start_campaign') {
                $sortTotalCredit = SortTotalCreditOfMyCampaignQueryBuilder::searchQuery($search, $searchBy)
                    ->OrWhereIn('uuid', $campaignUuids)
                    ->get()->sortByDesc('number_credit_needed_to_start_campaign');
            }
        }


        return $this->collectionPagination($sortTotalCredit, $perPage);
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

    public function myCampaigns($request, $contactLists = [])
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $columns = $request->get('columns', '*');
        $pageName = $request->get('page_name', 'page');
        $search = $request->get('search', '');
        $searchBy = $request->get('search_by', '');

        if (empty($contactLists)) {

            return $this->modelQueryBuilderClass::searchQuery($search, $searchBy)
                ->paginate($perPage, $columns, $pageName, $page);
        }
        $campaignUuids = $this->model->select('campaigns.*')
            ->join('campaign_contact_list', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->WhereIn('campaign_contact_list.contact_list_uuid', $contactLists)->get()->pluck('uuid');

        return $this->modelQueryBuilderClass::searchQuery($search, $searchBy)
            ->OrWhereIn('uuid', $campaignUuids)
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
