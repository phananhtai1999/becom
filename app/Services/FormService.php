<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Form;
use App\Models\QueryBuilders\FormQueryBuilder;
use Carbon\Carbon;

class FormService extends AbstractService
{
    protected $modelClass = Form::class;

    protected $modelQueryBuilderClass = FormQueryBuilder::class;

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findFormByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }

    public function showFormForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [Form::PENDING_PUBLISH_STATUS, Form::REJECT_PUBLISH_STATUS, Form::DRAFT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function showFormDefaultById($id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', Form::PUBLISHED_PUBLISH_STATUS],
            ['contact_list_uuid', null],
            ['uuid', $id]
        ]);
    }

    public function totalEditorFormChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 2, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as reject")
            ->where('user_uuid', auth()->user()->getKey())
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->first()->setAppends([])->toArray();
    }

    public function getFormChartByDateFormat($dateFormat, $startDate, $endDate)
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

    public function editorFormChart($groupBy, $startDate, $endDate)
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

        $charts = $this->getFormChartByDateFormat($dateFormat, $startDate, $endDate)->keyBy('label');
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
