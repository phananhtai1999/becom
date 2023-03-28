<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\ActiveStatusEvent;
use App\Events\ActivityHistoryOfSendByCampaignEvent;
use App\Events\SendNextByScenarioCampaignEvent;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MailSendingHistoryRequest;
use App\Http\Requests\UpdateMailSendingHistoryRequest;
use App\Http\Resources\MailSendingHistoryResourceCollection;
use App\Http\Resources\MailSendingHistoryResource;
use App\Services\CampaignScenarioService;
use App\Services\CampaignService;
use App\Services\ContactService;
use App\Services\MailOpenTrackingService;
use App\Services\MailSendingHistoryService;
use App\Services\MyMailSendingHistoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MailSendingHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait, RestIndexMyTrait;

    /**
     * @var MyMailSendingHistoryService
     */
    protected $myService;

    /**
     * @var MailOpenTrackingService
     */
    protected $mailOpenTrackingService;

    /**
     * @var CampaignService
     */
    protected $campaignService;

    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * @var CampaignScenarioService
     */
    protected $campaignScenarioService;

    /**
     * @param MailSendingHistoryService $service
     * @param MyMailSendingHistoryService $myService
     * @param MailOpenTrackingService $mailOpenTrackingService
     * @param CampaignService $campaignService
     * @param ContactService $contactService
     * @param CampaignScenarioService $campaignScenarioService
     */
    public function __construct(
        MailSendingHistoryService   $service,
        MyMailSendingHistoryService $myService,
        MailOpenTrackingService     $mailOpenTrackingService,
        CampaignService             $campaignService,
        ContactService              $contactService,
        CampaignScenarioService     $campaignScenarioService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->mailOpenTrackingService = $mailOpenTrackingService;
        $this->resourceCollectionClass = MailSendingHistoryResourceCollection::class;
        $this->resourceClass = MailSendingHistoryResource::class;
        $this->storeRequest = MailSendingHistoryRequest::class;
        $this->editRequest = UpdateMailSendingHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->campaignService = $campaignService;
        $this->contactService = $contactService;
        $this->campaignScenarioService = $campaignScenarioService;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyMailSendingHistory($id)
    {
        $model = $this->myService->findMyMailSendingHistoryByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function mailOpenTracking(Request $request, $id)
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $this->mailOpenTrackingService->mailOpenTracking($id, $ip, $userAgent);

        $mailSendingHistory = $this->service->findOneById($id);
        $statusMailSendingHistory = $mailSendingHistory->status;
        if ($statusMailSendingHistory !== "opened") {
            $this->service->update($mailSendingHistory, ['status' => 'opened']);
            ActivityHistoryOfSendByCampaignEvent::dispatch($mailSendingHistory, $mailSendingHistory->campaign->send_type, null);
        }
        if ($mailSendingHistory->campaign_scenario_uuid) {
            $campaignScenario = $this->campaignScenarioService->findOneById($mailSendingHistory->campaign_scenario_uuid);        //Add 1 point when open mail
            //Add 1 point when open mail or phone And Update status contact
            ActiveStatusEvent::dispatch($campaignScenario->getRoot()->campaign_uuid, $mailSendingHistory->email);
            if ($statusMailSendingHistory !== "opened") {
                if (($nextCampaignScenario = $this->campaignScenarioService->getCampaignWhenOpenEmailByUuid($mailSendingHistory->campaign_scenario_uuid, $mailSendingHistory->created_at))
                    && ($nextCampaign = $this->campaignService->checkActiveCampaignScenario($nextCampaignScenario->campaign_uuid))) {
                    if ($nextCampaign->send_type === "email") {
                        $contactOpenMail = $this->contactService->getContactByCampaignTypeEmail($nextCampaignScenario->getRoot()->campaign_uuid, $mailSendingHistory->email);
                    } else {
                        $contactOpenMail = $this->contactService->getContactByCampaignTypeSms($nextCampaignScenario->getRoot()->campaign_uuid, $mailSendingHistory->email);
                    }
                    SendNextByScenarioCampaignEvent::dispatch($nextCampaign, $contactOpenMail, $nextCampaignScenario);
                }
            }
        } else {
            //Add 1 point when open mail or phone And Update status contact
            ActiveStatusEvent::dispatch($mailSendingHistory->campaign_uuid, $mailSendingHistory->email);
        }

        return response(file_get_contents(public_path('tracking_pixel/pixel.gif')))
            ->header('content-type', 'image/gif');
    }

    /**
     * @param ChartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $total = $this->service->getTotalEmailTrackingChart($startDate, $endDate);
        $emailsChart = $this->service->getEmailTrackingChart($startDate, $endDate, $groupBy);

        return $this->sendOkJsonResponse([
            'data' => $emailsChart,
            'total' => $total
        ]);
    }

    /**
     * @param ChartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myEmailChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $myTotal = $this->myService->getTotalMyEmailTrackingChart($startDate, $endDate);
        $myEmailsChart = $this->myService->getMyEmailTrackingChart($startDate, $endDate, $groupBy);

        return $this->sendOkJsonResponse([
            'data' => $myEmailsChart,
            'total' => $myTotal
        ]);
    }
}
