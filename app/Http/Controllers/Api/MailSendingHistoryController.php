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
     * @param MailSendingHistoryService $service
     * @param MyMailSendingHistoryService $myService
     * @param MailOpenTrackingService $mailOpenTrackingService
     * @param CampaignService $campaignService
     * @param ContactService $contactService
     */
    public function __construct(
        MailSendingHistoryService $service,
        MyMailSendingHistoryService $myService,
        MailOpenTrackingService $mailOpenTrackingService,
        CampaignService $campaignService,
        ContactService $contactService
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
        //Add 1 point when open mail
        if ($mailSendingHistory->status !== "opened") {
            $this->contactService->addPointContactOpenMailCampaign($mailSendingHistory->campaign_uuid, $mailSendingHistory->email);
        }
        $this->service->update($mailSendingHistory, ['status' => 'opened']);

        //Send Email Scenario Campaign
        $campaign = $this->campaignService->findOneById($mailSendingHistory->campaign_uuid);
        if ($this->campaignService->checkScenarioCampaign($campaign, $mailSendingHistory)) {
            $contactOpenMail = $this->contactService->getContactByCampaign($campaign->uuid, $mailSendingHistory->email);
            $campaignScenario = $this->campaignService->findOneById($campaign->open_mail_campaign);
            $contactListCampaignScenario = $campaignScenario->contactlists;
            $contact = $this->contactService->checkAndInsertContactIntoContactList($contactOpenMail, $contactListCampaignScenario[0]->uuid);

            if ($this->campaignService->checkActiveScenarioCampaign($campaignScenario->uuid)) {
                if (($this->service->getNumberEmailSentByStatusAndCampaignUuid($campaignScenario->uuid, "sent") > 0 ||
                    $this->service->getNumberEmailSentByStatusAndCampaignUuid($campaignScenario->uuid, "opened") > 0) && ($this->service->getNumberEmailSentPerUser($campaignScenario->uuid, $contact->email) == 0)) {
                    SendNextEmailByScenarioCampaignEvent::dispatch($campaignScenario, $contact);
                }
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
            'total' => $total[0]
        ]);
    }

    public function myEmailChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $myTotal = $this->myService->getTotalMyEmailTrackingChart($startDate, $endDate);
        $myEmailsChart = $this->myService->getMyEmailTrackingChart($startDate, $endDate, $groupBy);

        return $this->sendOkJsonResponse([
            'data' => $myEmailsChart,
            'total' => $myTotal[0]
        ]);
    }
}
