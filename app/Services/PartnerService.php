<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\QueryBuilders\PartnerCategoryQueryBuilder;
use App\Models\QueryBuilders\PartnerQueryBuilder;
use App\Models\QueryBuilders\SectionCategoryQueryBuilder;
use App\Models\SectionCategory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;

class PartnerService extends AbstractService
{
    protected $modelClass = Partner::class;

    protected $modelQueryBuilderClass = PartnerQueryBuilder::class;

    public function sortByAttributeOfPartner($request)
    {
        $sort = $request->get('sort');
        $request = $this->getIndexRequest($request);
        if (!Str::startsWith($sort, '-')){
            $result =  $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])->get()
                ->sortBy($sort);
        }else{
            $result =  $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])->get()
                ->sortByDesc(ltrim($sort, '-'));
        }
        return $this->collectionPagination($result, $request['per_page'], $request['page']);
    }

    public function getPartnersChartByGroup($startDate, $endDate, $groupBy)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $times = [];
        $result = [];
        if ($groupBy == "date"){
            $dateFormat = "%Y-%m-%d";
            $partnersChart = $this->getPartnersChartByDateFormat($dateFormat, $startDate, $endDate)->keyBy('label');

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->addDay();
            }
        }

        if ($groupBy == "month"){
            $dateFormat = "%Y-%m";
            $partnersChart = $this->getPartnersChartByDateFormat($dateFormat, $startDate, $endDate)->keyBy('label');

            $period = CarbonPeriod::create($startDate->format('Y-m'), '1 month', $endDate->format('Y-m'));
            foreach ($period as $date) {
                $times[] = $date->format('Y-m');
            }
        }

        foreach ($times as $time) {
            $partnerByTime = $partnersChart->first(function ($item, $key) use ($time){
                return $key === $time;
            });

            if ($partnerByTime){
                $result[] = $partnerByTime->toArray();
            }else{
                $result [] = [
                  'label' => $time,
                  'active'  => 0,
                  'block'  => 0,
                  'pending'  => 0,
                ];
            }
        }

        return $result;
    }

    public function getPartnersChartByDateFormat($dateFormat, $startDate, $endDate)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,
        COUNT(IF( publish_status = 'active', 1, NULL ) ) as active,
        COUNT(IF( publish_status = 'block', 1, NULL ) ) as block,
        COUNT(IF( publish_status = 'pending', 1, NULL ) ) as pending")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }

    public function getTotalPartnersChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( publish_status = 'active', 1, NULL ) ) as active,
        COUNT(IF( publish_status = 'block', 1, NULL ) ) as block,
        COUNT(IF( publish_status = 'pending', 1, NULL ) ) as pending")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->first();
    }
}
