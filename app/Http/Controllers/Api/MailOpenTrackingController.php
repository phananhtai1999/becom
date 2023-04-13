<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ReportAnalyticDataCampaignRequest;
use App\Http\Requests\ReportAnalyticDataCampaignsRequest;
use App\Http\Resources\CampaignResource;
use App\Services\CampaignService;
use App\Services\MailOpenTrackingService;
use App\Services\MailSendingHistoryService;
use Carbon\Carbon;

class MailOpenTrackingController extends AbstractRestAPIController
{

    /**
     * @var CampaignService
     */
    protected $campaignService;

    /**
     * @var MailSendingHistoryService
     */
    protected $mailSendingHistoryService;

    /**
     * @param MailOpenTrackingService $service
     * @param CampaignService $campaignService
     * @param MailSendingHistoryService $mailSendingHistoryService
     */
    public function __construct(
        MailOpenTrackingService $service,
        CampaignService $campaignService,
        MailSendingHistoryService $mailSendingHistoryService
    )
    {
        $this->service = $service;
        $this->campaignService = $campaignService;
        $this->mailSendingHistoryService = $mailSendingHistoryService;

    }

    /**
     * @param ReportAnalyticDataCampaignsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportAnalyticDataCampaigns(ReportAnalyticDataCampaignsRequest $request)
    {
        $type = $request->get('type', 'day');
        if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
            $fromDate = $request->get('from_date');
        }elseif($type === 'day'){
            $fromDate = Carbon::now();
        }elseif($type === 'week'){
            $fromDate = Carbon::now()->subDay(7);
        }elseif ($type === 'month'){
            $fromDate = Carbon::now()->subMonth();
        }
        $toDate = $request->get('to_date', Carbon::now());
        $resultReport = $this->service->reportAnalyticDataCampaigns($fromDate, $toDate, $request->get('send_project_uuid'));

        $data = [];
        foreach ($resultReport as $result){
            $campaign = $this->campaignService->findCampaignByReport($result->campaign_uuid);
            $data[] = [
                'campaign' => $this->service->resourceToData(CampaignResource::class, $campaign)['data'],
                'opened' => $result->opened,
                'sent' => $this->mailSendingHistoryService->getNumberEmailSentByCampaign($result->campaign_uuid)
            ];
        }

        return $this->sendOkJsonResponse(['data' => $data]);
    }

    /**
     * @param ReportAnalyticDataCampaignRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportAnalyticDataCampaign(ReportAnalyticDataCampaignRequest $request, $id)
    {
        $type = $request->get('type', 'day');
        if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
            $fromDate = $request->get('from_date');
        }elseif($type === 'day'){
            $fromDate = Carbon::now();
        }elseif($type === 'week'){
            $fromDate = Carbon::now()->subDay(7);
        }elseif ($type === 'month'){
            $fromDate = Carbon::now()->subMonth();
        }
        $toDate = $request->get('to_date', Carbon::now());
        $numberOpen = $this->service->getNumberOpenMailByCampaignUuid($fromDate, $toDate, $id);

        return $this->sendOkJsonResponse(['data' => [
            'campaign' => $this->service->resourceToData(CampaignResource::class, $this->campaignService->findCampaignByReport($id))['data'],
            'opened' => $numberOpen,
            'sent' => $this->mailSendingHistoryService->getNumberEmailSentByCampaign($id)
        ]]);
    }
}
