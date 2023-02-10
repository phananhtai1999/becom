<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailByCampaginRootScenarioEvent;
use App\Events\SendEmailByCampaignEvent;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Requests\CampaignLinkTrackingRequest;
use App\Http\Requests\CampaignRequest;
use App\Http\Requests\ChartRequest;
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
use App\Services\ConfigService;
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
    use RestDestroyTrait;

    /**
     * @var MyCampaignService
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
     * @var ConfigService
     */
    protected $configService;

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
     * @param ConfigService $configService
     */
    public function __construct
    (
        CampaignService                  $service,
        MyCampaignService                $myService,
        CampaignTrackingService          $campaignTrackingService,
        CampaignDailyTrackingService     $campaignDailyTrackingService,
        CampaignLinkDailyTrackingService $campaignLinkDailyTrackingService,
        CampaignLinkTrackingService      $campaignLinkTrackingService,
        SendEmailByCampaignService       $sendEmailByCampaignService,
        SmtpAccountService               $smtpAccountService,
        MailTemplateVariableService      $mailTemplateVariableService,
        EmailService                     $emailService,
        SendEmailScheduleLogService      $sendEmailScheduleLogService,
        MailSendingHistoryService        $mailSendingHistoryService,
        ContactService                   $contactService,
        UserService                      $userService,
        ConfigService                    $configService
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
        $this->configService = $configService;
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function index(IndexRequest $request)
    {
        $sortTotalCredit = explode(',', $request->sort);

        if($sortTotalCredit[0] == 'number_credit_needed_to_start_campaign' || $sortTotalCredit[0] == '-number_credit_needed_to_start_campaign')
        {
            $models= $this->service->sortTotalCredit($request->get('per_page', '15'), $sortTotalCredit[0]);
        } else {
            $models =$this->service->getCollectionWithPagination();
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if ($request->get('type') === "scenario" && count($request->get('contact_list')) > 1) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['campaign' => __('messages.scenario_campaign_only_one_contact_list')]]);
        }

        if (empty($request->get('user_uuid'))) {
            $data = array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
            ]);
        } else {
            $data = $request->all();
        }

        $model = $this->service->create($data);

        $model->contactLists()->attach($request->get('contact_list'));

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

        if ($request->get('type') === "scenario" && count($request->get('contact_list')) > 1) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['campaign' => __('messages.scenario_campaign_only_one_contact_list')]]);
        }

        $model = $this->service->findOrFailById($id);
        if (!empty($request->get('send_type')) && $model->mailTemplate->type != $request->get('send_type')) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['send_type' => __('messages.send_type_campaign_error')]]);
        }
        $this->service->update($model, $request->all());

        $model->contactLists()->sync($request->contact_list ?? $model->contactLists);

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

        $failedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "fail");
        $openedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "opened");
        $sentCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "sent") + $openedCount;
        $emailCount = count($this->contactService->getContactsSendEmail($id));
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
        $sortTotalCredit = explode(',', $request->sort);

        if($sortTotalCredit[0] == 'number_credit_needed_to_start_campaign' || $sortTotalCredit[0] == '-number_credit_needed_to_start_campaign')
        {
            $models= $this->myService->sortMyTotalCredit($request->get('per_page', '15'), $sortTotalCredit[0]);
        } else {
            $models =$this->myService->getCollectionWithPagination();
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param MyCampaignRequest $request
     * @return JsonResponse
     */
    public function storeMyCampaign(MyCampaignRequest $request)
    {
        $user = $this->userService->findOrFailById(auth()->user()->getkey());
        $configSmtpAuto = $this->configService->findConfigByKey('smtp_auto');

        if ($request->get('type') === "scenario" && count($request->get('contact_list')) > 1) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['campaign' => __('messages.scenario_campaign_only_one_contact_list')]]);
        }

        if ($request->get('send_type') === "email") {
            if (($user->can_add_smtp_account == 1 || $configSmtpAuto->value == 0)) {
                if (empty($request->get('smtp_account_uuid'))) {
                    return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                }
            } else {
                if (!empty($request->get('smtp_account_uuid'))) {
                    return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                }
            }
        }

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

        $failedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "fail");
        $openedCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "opened");
        $sentCount = $this->mailSendingHistoryService->getNumberEmailSentByStatusAndCampaignUuid($id, "sent") + $openedCount;
        $emailCount = count($this->contactService->getContactsSendEmail($id));
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

        $user = $this->userService->findOrFailById(auth()->user()->getkey());
        $config = $this->configService->findConfigByKey('smtp_auto');

        if ($request->get('type') === "scenario" && count($request->get('contact_list')) > 1) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['campaign' => __('messages.scenario_campaign_only_one_contact_list')]]);
        }

        if (!empty($request->get('send_type')) && $model->mailTemplate->type != $request->get('send_type')) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['send_type' => __('messages.send_type_campaign_error')]]);
        }

        if (array_key_exists('smtp_account_uuid', $request->all()) && (($request->get('send_type') ?? $model->send_type ) === "email")) {
            if (($user->can_add_smtp_account == 1 || $config->value == 0)){
                if (empty($request->get('smtp_account_uuid'))) {
                    return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                }
            } else {
                if (!empty($request->get('smtp_account_uuid'))) {
                    return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                }
            }
        }

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->contactLists()->sync($request->contact_list ?? $model->contactLists);

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
        //validate campaign
        $columns = ['type', 'status', 'from_date', 'to_date', 'was_finished', 'was_stopped_by_owner'];
        foreach ($columns as $column) {
            if (!$this->service->checkActiveCampainByColumn($column, $request->get('campaign_uuid'))) {
                return $this->sendValidationFailedJsonResponse(["errors" => ["campaign_" . $column => __("messages.{$column}_campaign_invalid")]]);
            }
        }

        $campaign = $this->service->findOneById($request->get('campaign_uuid'));
        $campaignsScenario = $campaign->campaignsScenario;
        $campaignRootScenario = $campaignsScenario->filter(function ($value) {
            return $value->parent_uuid === null;
        });
        if ($this->sendEmailScheduleLogService->checkActiveCampaignbyCampaignUuid($request->get('campaign_uuid'))) {
            $creditNumberSendEmail = $campaign->number_credit_needed_to_start_campaign * ($campaignRootScenario->count() > 0 ? $campaignRootScenario->count() : 1 );
            if ($this->userService->checkCreditToSendEmail($creditNumberSendEmail, $campaign->user_uuid)) {
                if ($campaign->send_type === "email") {
                    if ($campaignRootScenario->count()) {
                        SendEmailByCampaginRootScenarioEvent::dispatch($campaign, $creditNumberSendEmail, $campaignRootScenario);
                    }else{
                        SendEmailByCampaignEvent::dispatch($campaign, $creditNumberSendEmail);
                    }
                }else{
                    // TO DO SMS
                }

                return $this->sendOkJsonResponse(["message" => __('messages.send_campaign_success')]);
            }
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['campaign_is_running' => __('messages.is_running_campaign_invalid')]]);
    }

    /**
     * @param SendEmailByMyCampaignRequest $request
     * @return void|JsonResponse
     */
    public function sendEmailByMyCampaign(SendEmailByMyCampaignRequest $request)
    {
        //validate campaign
        $columns = ['type', 'status', 'from_date', 'to_date', 'was_finished', 'was_stopped_by_owner'];
        foreach ($columns as $column) {
            if (!$this->myService->checkActiveMyCampainByColumn($column, $request->get('campaign_uuid'))) {
                return $this->sendValidationFailedJsonResponse(["errors" => ["campaign_" . $column => __("messages.{$column}_campaign_invalid")]]);
            }
        }

        $campaign = $this->myService->findMyCampaignByKeyOrAbort($request->get('campaign_uuid'));
        $campaignsScenario = $campaign->campaignsScenario;
        $campaignRootScenario = $campaignsScenario->filter(function ($value) {
            return $value->parent_uuid === null;
        });
        if ($this->sendEmailScheduleLogService->checkActiveCampaignbyCampaignUuid($request->get('campaign_uuid'))) {
            $creditNumberSendEmail = $campaign->number_credit_needed_to_start_campaign * ($campaignRootScenario->count() > 0 ? $campaignRootScenario->count() : 1 );
            if ($this->userService->checkCreditToSendEmail($creditNumberSendEmail, $campaign->user_uuid)) {
                if ($campaign->send_type === "email") {
                    if ($campaignRootScenario->count()) {
                        SendEmailByCampaginRootScenarioEvent::dispatch($campaign, $creditNumberSendEmail, $campaignRootScenario);
                    }else{
                        SendEmailByCampaignEvent::dispatch($campaign, $creditNumberSendEmail);
                    }
                }else {
                    //TO DO SMS
                }

                return $this->sendOkJsonResponse(["message" => __('messages.send_campaign_success')]);
            }

            return $this->sendValidationFailedJsonResponse(["errors" => ['credit' => __('messages.credit_invalid')]]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['campaign_is_running' => __('messages.is_running_campaign_invalid')]]);
    }

    /**
     * @param ChartRequest $request
     * @return JsonResponse
     */
    public function campaignChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $totalActiveAndOther = $this->service->getTotalActiveAndOtherCampaignChart($startDate, $endDate);
        $totalRunning = $this->sendEmailScheduleLogService->getTotalRunningCampaignChart($startDate, $endDate);
        $campaignsChart = $this->service->getCampaignChart($startDate, $endDate, $groupBy);

        return $this->sendOkJsonResponse([
            'data' => $campaignsChart,
            'total' => [
                'running' => $totalRunning,
                'active' => $totalActiveAndOther->active,
                'other' => $totalActiveAndOther->other
            ]
        ]);
    }

    /**
     * @param ChartRequest $request
     * @return JsonResponse
     */
    public function myCampaignChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $totalActiveAndOtherMyCampaign = $this->myService->getTotalActiveAndOtherMyCampaignChart($startDate, $endDate);
        $totalRunningMyCampaign = $this->sendEmailScheduleLogService->getTotalRunningMyCampaignChart($startDate, $endDate);
        $myCampaignsChart = $this->myService->getMyCampaignChart($startDate, $endDate, $groupBy);

        return $this->sendOkJsonResponse([
            'data' => $myCampaignsChart,
            'total' => [
                'running' => $totalRunningMyCampaign,
                'active' => $totalActiveAndOtherMyCampaign->active,
                'other' => $totalActiveAndOtherMyCampaign->other
            ]
        ]);
    }

    /**
     * @param SendEmailByCampaignRequest $request
     * @return JsonResponse
     */
    public function testSendEmailByCampaign(SendEmailByCampaignRequest $request)
    {
        $campaign = $this->service->getInfoRelationshipCampaignByUuid($request->campaign_uuid);
        $user = auth()->user();
        $config = $this->configService->findConfigByKey('smtp_auto');
        try {
            if($user->can_add_smtp_account == 1 || $config->value == 0){
                if(!empty($campaign->smtpAccount)){
                    $smtpAccount = $campaign->smtpAccount;
                }else{
                    $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
                }
            }else{
                $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
            }
            $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccount);
            $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $user->email, $smtpAccount, $campaign);
            Mail::to($user->email)->send(new SendCampaign($mailTemplate, $smtpAccount->mail_from_name, $smtpAccount->mail_from_address));
            return $this->sendOkJsonResponse(['message' => __('messages.test_send_campaign_success')]);
        }catch (\Exception $e){
            return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
        }
    }

    /**
     * @param SendEmailByMyCampaignRequest $request
     * @return JsonResponse
     */
    public function testSendEmailByMyCampaign(SendEmailByMyCampaignRequest $request)
    {
        $campaign = $this->service->getInfoRelationshipCampaignByUuid($request->campaign_uuid);
        $user = auth()->user();
        $config = $this->configService->findConfigByKey('smtp_auto');
        try {
            if($user->can_add_smtp_account == 1 || $config->value == 0){
                if(!empty($campaign->smtpAccount)){
                    $smtpAccount = $campaign->smtpAccount;
                }else{
                    $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
                }
            }else{
                $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
            }
            $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccount);
            $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $user->email, $smtpAccount, $campaign);
            Mail::to($user->email)->send(new SendCampaign($mailTemplate, $smtpAccount->mail_from_name, $smtpAccount->mail_from_address));
            return $this->sendOkJsonResponse(['message' => __('messages.test_send_campaign_success')]);
        }catch (\Exception $e){
            return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
        }
    }
}
