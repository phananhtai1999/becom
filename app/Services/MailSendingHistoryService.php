<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;
use App\Models\QueryBuilders\MailSendingHistoryQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\True_;

class MailSendingHistoryService extends AbstractService
{
    protected $modelClass = MailSendingHistory::class;

    protected $modelQueryBuilderClass = MailSendingHistoryQueryBuilder::class;

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getNumberEmailSentPerUserByCampaignUuid($campaignUuid)
    {
        return $this->model->select('email', DB::raw('COUNT(email) AS quantity_email_per_user'))
            ->where('campaign_uuid', $campaignUuid)
            ->groupBy('email')
            ->first();
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return mixed
     */
    public function getNumberEmailSentPerUser($campaignUuid, $email)
    {
        return $this->model->where('campaign_uuid', $campaignUuid)
            ->where('email', $email)
            ->whereNotIn('status', ["fail"])
            ->groupBy('email')
            ->count();
    }

    /**
     * @param $campaign
     * @param $emails
     * @return bool
     */
    public function checkTodayNumberEmailSentUser($campaign, $toEmails){
        foreach($toEmails as $email){
            $numberEmailSent =  $this->model->where('campaign_uuid', $campaign->uuid)
                ->where('email', $email)
                ->whereDate('time', Carbon::now())
                ->groupBy('email')
                ->count();

            if($numberEmailSent >= $campaign->number_email_per_date){
                return false;
            }
        }

        return true;
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getNumberEmailSentByCampaign($campaignUuid)
    {
        return $this->model->where('campaign_uuid', $campaignUuid)->groupBy('campaign_uuid')->count();
    }

    /**
     * @param $campaignUuid
     * @param $status
     * @return mixed
     */
    public function getNumberEmailSentByStatusAndCampaignUuid($campaignUuid, $status)
    {
        return $this->model->where([
            ['campaign_uuid', $campaignUuid],
            ['status', $status]
        ])->count();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $groupBy
     * @return array
     */
    public function getEmailTrackingChart($startDate, $endDate, $groupBy)
    {
        $startDate = Carbon::parse($startDate);
        $times = [];
        $result = [];
        $check = true;

        if($groupBy === "hour"){
            $emailsChart = $this->createQueryGetEmailChart("%Y-%m-%d %H:00:00", $startDate, $endDate);
            $endDate = Carbon::parse($endDate)->endOfDay();

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d H:00:00');
                $startDate = $startDate->addHour();
            }
        }
        if($groupBy === "date"){
            $emailsChart = $this->createQueryGetEmailChart("%Y-%m-%d", $startDate, $endDate);
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m-d');
                $startDate = $startDate->addDay();
            }
        }
        if($groupBy === "month"){
            $emailsChart = $this->createQueryGetEmailChart("%Y-%m", $startDate, $endDate);
            $endDate = Carbon::parse($endDate);

            while($startDate <= $endDate){
                $times[] = $startDate->format('Y-m');
                $startDate = $startDate->addMonth();
            }
        }
        foreach ($times as $time){
            if(!empty($emailsChart)){
                foreach ($emailsChart as $emailChart){
                    if(in_array($time, $emailChart)){
                        $result[] = $emailChart;
                        $check = true;
                        break;
                    }else{
                        $check = false;
                    }
                }
                if(!$check){
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
    public function getTotalEmailTrackingChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( status = 'sent', 1, NULL ) ) as sent, COUNT(IF( status = 'opened', 1, NULL ) ) as opened")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)->get();

    }

    /**
     * @param $dateFormat
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function createQueryGetEmailChart($dateFormat, $startDate, $endDate){
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,  COUNT(IF( status = 'sent', 1, NULL ) ) as sent, COUNT(IF( status = 'opened', 1, NULL ) ) as opened")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get()->toArray();
    }
}
