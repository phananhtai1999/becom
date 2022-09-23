<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailByCampaignEvent;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Requests\CampaignLinkTrackingRequest;
use App\Http\Requests\CampaignRequest;
use App\Http\Requests\IncrementCampaignTrackingRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\LoadAnalyticDataRequest;
use App\Http\Requests\MyCampaignRequest;
use App\Http\Requests\SendEmailByCampaignRequest;
use App\Http\Requests\SendEmailByMyCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Requests\UpdateMyCampaignRequest;
use App\Http\Resources\CampaignDailyTrackingResourceCollection;
use App\Http\Resources\CampaignResourceCollection;
use App\Http\Resources\CampaignDailyTrackingResource;
use App\Http\Resources\CampaignLinkDailyTrackingResource;
use App\Http\Resources\CampaignLinkTrackingResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignTrackingResource;
use App\Mail\SendCampaign;
use App\Services\CampaignDailyTrackingService;
use App\Services\CampaignLinkDailyTrackingService;
use App\Services\CampaignLinkTrackingService;
use App\Services\CampaignService;
use App\Services\CampaignTrackingService;
use App\Services\ContactService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailByCampaignService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Carbon\Carbon;
use App\Services\MyCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class CampaignController extends AbstractRestAPIController
{
    use RestIndexTrait, RestDestroyTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @var CampaignTrackingService
     */
    protected $campaignTrackingService;

    /**
     * @var CampaignDailyTrackingService
     */
    protected $campaignDailyTrackingService;

    /**
     * @var CampaignLinkTrackingService
     */
    protected $campaignLinkTrackingService;

    /**
     * @var CampaignLinkDailyTrackingService
     */
    protected $campaignLinkDailyTrackingService;

    /**
     * @var SendEmailByCampaignService
     */
    protected $sendEmailByCampaignService;

    /**
     * @var SmtpAccountService
     */
    protected $smtpAccountService;

    /**
     * @var MailTemplateVariableService
     */
    protected $mailTemplateVariableService;

    /**
     * @var EmailService
     */
    protected $emailService;

    /**
     * @var SendEmailScheduleLogService
     */
    protected $sendEmailScheduleLogService;

    /**
     * @var MailSendingHistoryService
     */
    protected $mailSendingHistoryService;

    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param CampaignService $service
     * @param MyCampaignService $myService
     * @param CampaignTrackingService $campaignTrackingService
     * @param CampaignDailyTrackingService $campaignDailyTrackingService
     * @param CampaignLinkDailyTrackingService $campaignLinkDailyTrackingService
     * @param CampaignLinkTrackingService $campaignLinkTrackingService
     * @param SendEmailByCampaignService $sendEmailByCampaignService
     * @param SmtpAccountService $smtpAccountService
     * @param MailTemplateVariableService $mailTemplateVariableService
     * @param EmailService $emailService
     * @param SendEmailScheduleLogService $sendEmailScheduleLogService
     * @param MailSendingHistoryService $mailSendingHistoryService
     * @param ContactService $contactService
     * @param UserService $userService
     */
    public function __construct
    (
        CampaignService $service,
        MyCampaignService $myService,
        CampaignTrackingService $campaignTrackingService,
        CampaignDailyTrackingService $campaignDailyTrackingService,
        CampaignLinkDailyTrackingService $campaignLinkDailyTrackingService,
        CampaignLinkTrackingService $campaignLinkTrackingService,
        SendEmailByCampaignService $sendEmailByCampaignService,
        SmtpAccountService $smtpAccountService,
        MailTemplateVariableService $mailTemplateVariableService,
        EmailService $emailService,
        SendEmailScheduleLogService $sendEmailScheduleLogService,
        MailSendingHistoryService $mailSendingHistoryService,
        ContactService $contactService,
        UserService $userService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = CampaignResourceCollection::class;
        $this->resourceClass = CampaignResource::class;
        $this->storeRequest = CampaignRequest::class;
        $this->editRequest = UpdateCampaignRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->campaignTrackingService = $campaignTrackingService;
        $this->campaignDailyTrackingService = $campaignDailyTrackingService;
        $this->campaignLinkTrackingService = $campaignLinkTrackingService;
        $this->campaignLinkDailyTrackingService = $campaignLinkDailyTrackingService;
        $this->sendEmailByCampaignService = $sendEmailByCampaignService;
        $this->smtpAccountService = $smtpAccountService;
        $this->mailTemplateVariableService = $mailTemplateVariableService;
        $this->emailService = $emailService;
        $this->sendEmailScheduleLogService = $sendEmailScheduleLogService;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->contactService = $contactService;
        $this->userService = $userService;
    }

    /**
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if(empty($request->get('user_uuid'))){
            $data = array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
            ]);
        }else{
            $data = $request->all();
        }
        $model = $this->service->create($data);

        $model->contactLists()->attach($request->get('contact_list', []));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        $contactListUuid = $this->service->findContactListKeyByCampaign($model);

        if ($contactListUuid == null)
        {
            $model->contactLists()->sync($request->get('contact_list', []));
        } else {
            $model->contactLists()->sync($request->get('contact_list', $contactListUuid));
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $model = $this->service->findOrFailById($id);
        $sentCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "sent");
        $failedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "fail");
        $openedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "opened");
        $emailCount = $model->number_email_per_user * count($this->contactService->getContactsSendEmail($id));
        $campaign = $this->service->resourceToData($this->resourceClass, $model)['data'];

        return $this->sendOkJsonResponse(['data' => array_merge($campaign, [
            'email_count' => $emailCount,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'opened_count' => $openedCount
        ])]);
    }

    /**
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyCampaign(IndexRequest $request)
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
     * @param MyCampaignRequest $request
     * @return JsonResponse
     */
    public function storeMyCampaign(MyCampaignRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->contactLists()->attach($request->get('contact_list', []));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMyCampaign($id)
    {
        $model = $this->myService->findMyCampaignByKeyOrAbort($id);

        $sentCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "sent");
        $failedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "fail");
        $openedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "opened");
        $emailCount = $model->number_email_per_user * count($this->contactService->getContactsSendEmail($id));
        $campaign = $this->service->resourceToData($this->resourceClass, $model)['data'];

        return $this->sendOkJsonResponse(['data' => array_merge($campaign, [
            'email_count' => $emailCount,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'opened_count' => $openedCount
        ])]);
    }

    /**
     * @param UpdateMyCampaignRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMyCampaign(UpdateMyCampaignRequest $request, $id)
    {
        $model = $this->myService->findMyCampaignByKeyOrAbort($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $contactListUuid = $this->myService->findContactListKeyByMyCampaign($model);

        if ($contactListUuid == null)
        {
            $model->contactLists()->sync($request->get('contact_list', []));
        }

        $model->contactLists()->sync($request->get('contact_list', $contactListUuid));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMyCampaign($id)
    {
        $this->myService->deleteMyCampaignByKey($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IncrementCampaignTrackingRequest $request
     * @return JsonResponse
     */
    public function incrementCampaignTrackingTotalOpen(IncrementCampaignTrackingRequest $request)
    {
        $campaignTrackingData = $this->service->resourceToData(
            CampaignTrackingResource::class,
            $this->campaignTrackingService->incrementTotalOpenByCampaignUuid($request->get('campaign_uuid'))
        );

        $campaignDailyTrackingData = $this->service->resourceToData(
            CampaignDailyTrackingResource::class,
            $this->campaignDailyTrackingService->incrementTotalOpenByCampaignUuid($request->get('campaign_uuid'))
        );

        return $this->sendOkJsonResponse([
            'data' => [
                'campaignTracking' => $campaignTrackingData['data'],
                'campaignDailyTracking' => $campaignDailyTrackingData['data'],
            ]
        ]);
    }

    /**
     * @param CampaignLinkTrackingRequest $request
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function upsertCampaignLinkTrackingTotalClick(CampaignLinkTrackingRequest $request)
    {
        $campaignLinkTrackingData = $this->service->resourceToData(
            CampaignLinkTrackingResource::class,
            $this->campaignLinkTrackingService->incrementTotalClickByCampaignUuid($request->get('campaign_uuid'))
        );

        $campaignLinkDailyTrackingData = $this->service->resourceToData(
            CampaignLinkDailyTrackingResource::class,
            $this->campaignLinkDailyTrackingService->incrementTotalClickByCampaignUuid($request->get('campaign_uuid'))
        );

        $campaignTrackingData = $this->service->resourceToData(
            CampaignTrackingResource::class,
            $this->campaignTrackingService->incrementTotalLinkClickByCampaignUuid($request->get('campaign_uuid'))
        );

        $campaignDailyTrackingData = $this->service->resourceToData(
            CampaignDailyTrackingResource::class,
            $this->campaignDailyTrackingService->incrementTotalLinkClickByCampaignUuid($request->get('campaign_uuid'))
        );

        return $this->sendOkJsonResponse([
            'data' => [
                'campaignLinkTracking' => $campaignLinkTrackingData['data'],
                'campaignLinkDailyTracking' => $campaignLinkDailyTrackingData['data'],
                'campaignTracking' => $campaignTrackingData['data'],
                'campaignDailyTracking' => $campaignDailyTrackingData['data'],
            ]
        ]);
    }

    /**
     * @param LoadAnalyticDataRequest $request
     * @return JsonResponse|void
     */
    public function loadAnalyticData(LoadAnalyticDataRequest $request)
    {
        $type = $request->get('type', 'daily');
        $toDate = $request->get('to_date', Carbon::today());
        $fromDate = $request->get('from_date', Carbon::today()->subMonth(12));

        if ($type == 'daily') {

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData(
                    CampaignDailyTrackingResourceCollection::class,
                    $this->campaignDailyTrackingService->loadCampaignDailyTrackingAnalytic($fromDate, $toDate)
                )
            );
        }
    }

    /**
     * @param SendEmailByCampaignRequest $request
     * @return void|JsonResponse
     */
    public function sendEmailsByCampaign(SendEmailByCampaignRequest $request)
    {
        $campaign = $this->service->findOneById($request->get('campaign_uuid'));
        if($this->sendEmailScheduleLogService->checkActiveCampaignbyCampaignUuid($request->get('campaign_uuid'))){

            $contactsNumberSendEmail = count($this->contactService->getContactsSendEmail($campaign->uuid));
            $creditNumberSendEmail = $contactsNumberSendEmail * config('credit.default_credit') * $campaign->number_email_per_date;
            if($this->userService->checkCreditToSendCEmail($creditNumberSendEmail, $campaign->user_uuid)){
                SendEmailByCampaignEvent::dispatch($campaign, $creditNumberSendEmail);

                return $this->sendOkJsonResponse(["message" => __('messages.send_campaign_success')]);
            }

            return $this->sendValidationFailedJsonResponse(["errors" => ['credit' =>  __('messages.credit_invalid')]]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['campaign_uuid' => __('messages.campaign_invalid')]]);
    }

    /**
     * @param SendEmailByMyCampaignRequest $request
     * @return void|JsonResponse
     */
    public function sendEmailByMyCampaign(SendEmailByMyCampaignRequest $request)
    {
        $campaign = $this->service->findOneById($request->get('campaign_uuid'));
        if($this->sendEmailScheduleLogService->checkActiveCampaignbyCampaignUuid($request->get('campaign_uuid'))){

            $contactsNumberSendEmail = count($this->contactService->getContactsSendEmail($campaign->uuid));
            $creditNumberSendEmail = $contactsNumberSendEmail * config('credit.default_credit') * $campaign->number_email_per_date;
            if($this->userService->checkCreditToSendCEmail($creditNumberSendEmail, $campaign->user_uuid)){
                SendEmailByCampaignEvent::dispatch($campaign, $creditNumberSendEmail);

                return $this->sendOkJsonResponse(["message" => __('messages.send_campaign_success')]);
            }

            return $this->sendValidationFailedJsonResponse(["errors" => ['credit' =>  __('messages.credit_invalid')]]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['campaign_uuid' => __('messages.campaign_invalid')]]);
    }
}
