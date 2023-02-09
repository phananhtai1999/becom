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
    public function getNumberEmailByCampaign($campaignUuid, $email) // Lấy số lượng email đã gửi theo campaign
    {
        return $this->model->where('campaign_uuid', $campaignUuid)
            ->whereNull('campaign_scenario_uuid')
            ->where('email', $email)
            ->whereNotIn('status', ["fail"])
            ->groupBy('email')
            ->count();
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return mixed
     */
    public function getNumberEmailByCampaignScenario($campaignUuid, $email, $campaignScenarioUuid)
    {
        return $this->model
            ->where([
                ['campaign_uuid', $campaignUuid],
                ['campaign_scenario_uuid', $campaignScenarioUuid],
                ['email', $email]
            ])
            ->whereNotIn('status', ["fail"])
            ->groupBy('email')
            ->count();
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
//        foreach ($times as $time){
//            if(!empty($emailsChart)){
//                foreach ($emailsChart as $emailChart){
//                    if(in_array($time, $emailChart)){
//                        $result[] = $emailChart;
//                        $check = true;
//                        break;
//                    }else{
//                        $check = false;
//                    }
//                }
//                if(!$check){
//                    $result[] = [
//                        'label' => $time,
//                        'sent' => 0,
//                        'opened' => 0
//                    ];
//                }
//            }else{
//                $result[] = [
//                    'label' => $time,
//                    'sent' => 0,
//                    'opened' => 0
//                ];
//            }
//        }
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
            ->whereDate('updated_at', '<=', $endDate)->first();

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
            ->get();
    }

    /**
     * @return mixed
     */
    public function getMailNotOpenHistories()
    {
         return $this->model->with('campaignScenario')->select("mail_sending_history.*")
             ->join('campaign_scenario as c', 'c.parent_uuid', '=', 'mail_sending_history.campaign_scenario_uuid')
             ->where('c.type', 'not_open')
             ->where('mail_sending_history.status', 'sent')
             ->whereRaw('CURDATE() - date(mail_sending_history.updated_at) > c.open_within')
             ->whereNotIn('c.uuid', function ($query) {
                    $query->select('m.campaign_scenario_uuid')
                        ->from('mail_sending_history as m')
                        ->whereNotNull('m.campaign_scenario_uuid')
                        ->where('m.status', "sent")
                        ->whereRaw("m.email = mail_sending_history.email");
             })->get();
        /*
         * select `mail_sending_history`.*
        from `mail_sending_history` inner join `campaign_scenario` on `campaign_scenario`.`parent_uuid` = `mail_sending_history`.`campaign_scenario_uuid`
        where `campaign_scenario`.`type` = "not_open" and `mail_sending_history`.`status` = "sent"
        and CURDATE() - date(mail_sending_history.updated_at) > campaign_scenario.open_within and
        `campaign_scenario`.`uuid` not in (select `m`.`campaign_scenario_uuid`
                                   from `mail_sending_history` as `m`
                                   where `m`.`campaign_scenario_uuid` is not null and `m`.`status` = "sent" and 			`m`.`email` = mail_sending_history.email) and `mail_sending_history`.`deleted_at` is null;
         * */

        //Kiểm tra trường hợp not open chưa tồn tại trong mailsendinghistory

    }


}
