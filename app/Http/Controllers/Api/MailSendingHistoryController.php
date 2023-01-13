<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailNotOpenByScenarioCampaignEvent;
use App\Events\SendNextEmailByScenarioCampaignEvent;
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
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

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
        MailSendingHistoryService $service,
        MyMailSendingHistoryService $myService,
        MailOpenTrackingService $mailOpenTrackingService,
        CampaignService $campaignService,
        ContactService $contactService,
        CampaignScenarioService $campaignScenarioService
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyMailSendingHistory(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    $request->get('per_page', '15'),
                    $request->get('page', '1'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
                )
            )
        );
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

        //Send Email Scenario Campaign
        if ($mailSendingHistory->status !== "opened") {
            //Add 1 point when open mail
            $this->service->update($mailSendingHistory, ['status' => 'opened']);
            if ($mailSendingHistory->campaign_scenario_uuid){
                $campaignScenario = $this->campaignScenarioService->findOneById($mailSendingHistory->campaign_scenario_uuid);
                $this->contactService->addPointContactOpenMailCampaign($campaignScenario->getRoot()->campaign_uuid, $mailSendingHistory->email);
                if(($nextCampaignScenario = $this->campaignScenarioService->getCampaignWhenOpenEmailByUuid($mailSendingHistory->campaign_scenario_uuid, $mailSendingHistory->created_at))
                && ($nextCampaign = $this->campaignService->checkActiveCampaignScenario($nextCampaignScenario->campaign_uuid))) {
                    $contactOpenMail = $this->contactService->getContactByCampaign($nextCampaignScenario->getRoot()->campaign_uuid, $mailSendingHistory->email);
                    if ($nextCampaign->send_type === "email") {
                        SendNextEmailByScenarioCampaignEvent::dispatch($nextCampaign, $contactOpenMail, $nextCampaignScenario);
                    } else {
                        // To DO SMS
                    }
                }
            }else{
                $this->contactService->addPointContactOpenMailCampaign($mailSendingHistory->campaign_uuid, $mailSendingHistory->email);
            }
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
