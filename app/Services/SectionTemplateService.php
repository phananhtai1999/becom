<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SectionTemplateQueryBuilder;
use App\Models\SectionTemplate;
use Carbon\Carbon;

class SectionTemplateService extends AbstractService
{
    protected $modelClass = SectionTemplate::class;

    protected $modelQueryBuilderClass = SectionTemplateQueryBuilder::class;

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findSectionTemplateByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }

    public function showSectionTemplateForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [SectionTemplate::PENDING_PUBLISH_STATUS, SectionTemplate::REJECT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function showSectionTemplateDefaultById($id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', SectionTemplate::PUBLISHED_PUBLISH_STATUS],
            ['is_default', true],
            ['uuid', $id]
        ]);
    }

    public function totalEditorSectionChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 2, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as reject")
            ->where('user_uuid', auth()->user()->getKey())
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->first()->setAppends([])->toArray();
    }

    public function getSectionChartByDateFormat($dateFormat, $startDate, $endDate)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,
        COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 2, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as reject")
            ->where('user_uuid', auth()->user()->getKey())
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }

    public function editorSectionChart($groupBy, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $currentDate = $startDate->copy();
        $times = [];
        $result = [];

        if ($groupBy == "hour"){
            $dateFormat = "%Y-%m-%d %H:00:00";

            $endDate = $endDate->endOfDay();
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d H:00:00');
                $currentDate = $currentDate->addHour();
            }
        }

        if ($groupBy == "date"){
            $dateFormat = "%Y-%m-%d";
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->addDay();
            }
        }

        if ($groupBy == "month"){
            $dateFormat = "%Y-%m";
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m');
                $currentDate = $currentDate->addMonth();
            }
        }

        $charts = $this->getSectionChartByDateFormat($dateFormat, $startDate, $endDate)->keyBy('label');
        foreach ($times as $time){
            $chartByTime = $charts->first(function($item, $key) use ($time){
                return $key == $time;
            });

            if($chartByTime){
                $result[] = [
                    'label' => $time,
                    'approve'  => $chartByTime->approve,
                    'pending'  => $chartByTime->pending,
                    'reject'  => $chartByTime->reject
                ];
            }else{
                $result [] = [
                    'label' => $time,
                    'approve'  => 0,
                    'pending'  => 0,
                    'reject'  => 0,
                ];
            }
        }

        return $result;
    }
}
