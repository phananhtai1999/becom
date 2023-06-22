<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;
use App\Models\QueryBuilders\MailTemplateQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MailTemplateService extends AbstractService
{
    protected $modelClass = MailTemplate::class;

    protected $modelQueryBuilderClass = MailTemplateQueryBuilder::class;

    /**
     * @param $id
     * @return bool
     */
    public function checkExistsMailTemplateInTables($id)
    {
        $mailTemplate = $this->findOrFailById($id);

        $campaigns = $mailTemplate->campaigns->toArray();

        if (!empty($campaigns)) {
            return true;
        }

        return false;
    }

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findMailTemplateByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }

    public function showMailTemplateForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [MailTemplate::PENDING_PUBLISH_STATUS, MailTemplate::REJECT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function moveBusinessCategoryOfMailTemplates($mailTemplates, $goBusinessCategoryUuid)
    {
        foreach ($mailTemplates as $mailTemplate){
            $this->update($mailTemplate, [
               'business_category_uuid' => $goBusinessCategoryUuid
            ]);
        }
    }

    public function movePurposeOfMailTemplates($mailTemplates, $goPurposeUuid)
    {
        foreach ($mailTemplates as $mailTemplate){
            $this->update($mailTemplate, [
                'purpose_uuid' => $goPurposeUuid
            ]);
        }
    }

    public function totalEditorMailTemplateChart($startDate, $endDate, $type = null)
    {
        return $this->model->selectRaw("COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 2, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as reject")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->where('user_uuid', auth()->user()->getKey())
            ->when($type, function ($q, $type) {
              $q->where('type', $type);
            })
            ->first()->toArray();
    }

    public function getMailTemplatesChartByDateFormat($dateFormat, $startDate, $endDate, $type = null)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,
        COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 2, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as reject")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->where('user_uuid', auth()->user()->getKey())
            ->when($type, function ($q, $type) {
                $q->where('type', $type);
            })
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }

    public function editorMailTemplateChart($groupBy, $startDate, $endDate, $type = null)
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

        $charts = $this->getMailTemplatesChartByDateFormat($dateFormat, $startDate, $endDate, $type)->keyBy('label');

        foreach ($times as $time){
            $mailByTime = $charts->first(function($item, $key) use ($time){
                return $key == $time;
            });

            if($mailByTime){
                $result[] = $mailByTime->toArray();
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
