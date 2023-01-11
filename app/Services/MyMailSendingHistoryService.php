<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;
use App\Models\QueryBuilders\MailSendingHistoryQueryBuilder;
use App\Models\QueryBuilders\MyMailSendingHistoryQueryBuilder;
use Carbon\Carbon;

class MyMailSendingHistoryService extends AbstractService
{
    protected $modelClass = MailSendingHistory::class;

    protected $modelQueryBuilderClass = MyMailSendingHistoryQueryBuilder::class;

    /**
     * @param $id
     * @return void
     */
    public function findMyMailSendingHistoryByKeyOrAbort($id)
    {
        $mailSendingHistory = $this->model->select('mail_sending_history.*')
            ->join('campaigns', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
            ->where([
                ['campaigns.user_uuid', auth()->user()->getKey()],
                ['mail_sending_history.uuid', $id]
            ])->first();

        if (!empty($mailSendingHistory)) {
            return $mailSendingHistory;
        } else {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $groupBy
     * @return array
     */
    public function getMyEmailTrackingChart($startDate, $endDate, $groupBy)
    {
        $startDate = Carbon::parse($startDate);
        $times = [];
        $result = [];

        if($groupBy === "hour"){
            $emailsChart = $this->createQueryGetMyEmailChart("%Y-%m-%d %H:00:00", $startDate, $endDate);
            $endDate = Carbon::parse($endDate)->endOfDay();

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d H:00:00');
                $startDate = $startDate->addHour();
            }
        }
        if($groupBy === "date"){
            $emailsChart = $this->createQueryGetMyEmailChart("%Y-%m-%d", $startDate, $endDate);
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d');
                $startDate = $startDate->addDay();
            }
        }
        if($groupBy === "month"){
            $emailsChart = $this->createQueryGetMyEmailChart("%Y-%m", $startDate, $endDate);
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m');
                $startDate = $startDate->addMonth();
            }
        }

        foreach ($times as $time){
            if(!empty($emailsChart)){
                $emailsChartByLabel = $emailsChart->keyBy('label');
                $emailChart = $emailsChartByLabel->first(function ($value, $key) use ($time) {
                    return $key === $time;
                });
                if ($emailChart) {
                    $result[] = $emailChart;
                } else {
                    $result[] = [
                        'label' => $time,
                        'sent' => 0,
                        'opened' => 0
                    ];
                }
            }else{
                $result[] = [
                    'label' => $time,
                    'sent' => 0,
                    'opened' => 0
                ];
            }
        }

        return $result;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $groupBy
     * @return mixed
     */
    public function getTotalMyEmailTrackingChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( mail_sending_history.status = 'sent', 1, NULL ) ) as sent, COUNT(IF( mail_sending_history.status = 'opened', 1, NULL ) ) as opened")
            ->join('campaigns', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
            ->whereDate('mail_sending_history.updated_at', '>=', $startDate)
            ->whereDate('mail_sending_history.updated_at', '<=', $endDate)
            ->where('campaigns.user_uuid', auth()->user()->getKey())->first();
    }

    /**
     * @param $dateFormat
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function createQueryGetMyEmailChart($dateFormat, $startDate, $endDate){
        return $this->model->selectRaw("date_format(mail_sending_history.updated_at, '{$dateFormat}') as label,  COUNT(IF( mail_sending_history.status = 'sent', 1, NULL ) ) as sent, COUNT(IF( mail_sending_history.status = 'opened', 1, NULL ) ) as opened")
            ->join('campaigns', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
            ->whereDate('mail_sending_history.updated_at', '>=', $startDate)
            ->whereDate('mail_sending_history.updated_at', '<=', $endDate)
            ->where('campaigns.user_uuid', auth()->user()->getKey())
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }
}
