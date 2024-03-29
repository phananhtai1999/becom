<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendByCampaignRootScenarioEvent;
use App\Events\SendByCampaignEvent;
use App\Events\SendNotificationSystemEvent;
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
use App\Http\Requests\StartCampaignRequest;
use App\Http\Requests\StartMyCampaignRequest;
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
use App\Models\Campaign;
use App\Models\Notification;
use App\Services\CampaignDailyTrackingService;
use App\Services\CampaignLinkDailyTrackingService;
use App\Services\CampaignLinkTrackingService;
use App\Services\CampaignScenarioService;
use App\Services\CampaignService;
use App\Services\CampaignTrackingService;
use App\Services\UserProfileService;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\ContactService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailByCampaignService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use App\Services\UserTeamService;
use Carbon\Carbon;
use App\Services\MyCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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

    protected $campaignScenarioService;

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
        ConfigService                    $configService,
        CampaignScenarioService          $campaignScenarioService,
        UserTeamService                  $userTeamService,
        UserProfileService               $userProfileService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->userProfileService = $userProfileService;
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
        $this->configService = $configService;
        $this->campaignScenarioService = $campaignScenarioService;
        $this->userTeamService = $userTeamService;
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

        if ($sortTotalCredit[0] == 'number_credit_needed_to_start_campaign' || $sortTotalCredit[0] == '-number_credit_needed_to_start_campaign') {
            $models = $this->service->sortTotalCredit($request->get('per_page', '15'), $sortTotalCredit[0], $request->search, $request->search_by);
        } else {
            $models = $this->service->getCollectionWithPagination();
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

        $data = array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

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
        if (!empty($request->get('send_type')) && empty($request->get('mail_template_uuid')) && $model->mailTemplate->type != $request->get('send_type')) {
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
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $model = $this->service->findOrFailById($id);
        if (count($this->campaignScenarioService->showCampaignScenarioByCampaignUuid($model->uuid))) {
            return $this->sendValidationFailedJsonResponse(["errors" => ["campaign_uuid" => __('messages.campaign_in_scenario')]]);
        }
        $this->service->destroy($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyCampaign(IndexRequest $request)
    {
        $sortTotalCredit = explode(',', $request->sort);
        $userTeam = $this->userTeamService->getUserTeamByUserAndAppId(auth()->userId(), auth()->appId());
        $user = $this->userProfileService->findOneWhereOrFail(['user_uuid' => auth()->userId(), 'app_id' => auth()->appId()]);
        if (($userTeam && !$userTeam['is_blocked']) && !empty($user->userTeamContactLists)) {
            if ($sortTotalCredit[0] == 'number_credit_needed_to_start_campaign' || $sortTotalCredit[0] == '-number_credit_needed_to_start_campaign') {
                $models = $this->myService->sortMyTotalCredit($request->get('per_page', '15'), $sortTotalCredit[0], $request->search, $request->search_by, $user->userTeamContactLists()->pluck('contact_list_uuid'));
            } else {
                $models = $this->myService->myCampaigns($request, $user->userTeamContactLists()->pluck('contact_list_uuid'));
            }
        } else {
            if ($sortTotalCredit[0] == 'number_credit_needed_to_start_campaign' || $sortTotalCredit[0] == '-number_credit_needed_to_start_campaign') {
                $models = $this->myService->sortMyTotalCredit($request->get('per_page', '15'), $sortTotalCredit[0], $request->search, $request->search_by);
            } else {
                $models = $this->myService->myCampaigns($request);
            }
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
        $user = $this->userProfileService->findOneWhereOrFail(['user_uuid' => auth()->userId()]);
        $configSmtpAuto = $this->configService->findConfigByKey('smtp_auto');

        if ($request->get('type') === "scenario" && count($request->get('contact_list')) > 1) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['campaign' => __('messages.scenario_campaign_only_one_contact_list')]]);
        }

        if (($user->can_add_smtp_account == 1 || $configSmtpAuto->value == 0) && $request->get('send_type') != "sms") {
            if (empty($request->get('smtp_account_uuid'))) {
                return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
            }
        } else {
            if (!empty($request->get('smtp_account_uuid'))) {
                return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
            }
        }

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
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

        $user = $this->userProfileService->findOneWhereOrFail(['user_uuid' => auth()->userId()]);
        $config = $this->configService->findConfigByKey('smtp_auto');

        if ($request->get('type') === "scenario" && count($request->get('contact_list')) > 1) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['campaign' => __('messages.scenario_campaign_only_one_contact_list')]]);
        }

        //Check send_type and mail_template same type
        if (!empty($request->get('send_type')) && empty($request->get('mail_template_uuid')) && $model->mailTemplate->type != $request->get('send_type')) {
            return $this->sendValidationFailedJsonResponse(["errors" => ['send_type' => __('messages.send_type_campaign_error')]]);
        }

        if (array_key_exists('smtp_account_uuid', $request->all())) {
            if (($user->can_add_smtp_account == 1 || $config->value == 0) && (($request->get('send_type') ?? $model->send_type) != "sms")) {
                if (empty($request->get('smtp_account_uuid'))) {
                    return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                }
            } else {
                if (!empty($request->get('smtp_account_uuid'))) {
                    return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                }
            }
        } else {
            //Check send_type and smtp_account same type
            if (!empty($request->get('send_type'))) {
                if (($user->can_add_smtp_account == 1 || $config->value == 0) && (($request->get('send_type') ?? $model->send_type) != "sms")) {
                    if (!$model->smtpAccount) {

                        return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                    } elseif (($request->get('send_type') === 'email' && $model->smtpAccount->mail_mailer != 'smtp') ||
                        ($request->get('send_type') != 'email' && $model->smtpAccount->mail_mailer != $request->get('send_type'))) {

                        return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account' => __('messages.smtp_account_error')]]);
                    }
                } else {
                    if ($model->smtpAccount) {

                        return $this->sendValidationFailedJsonResponse(["errors" => ['smtp_account_uuid' => __('messages.smtp_account_invalid')]]);
                    }
                }
            }
        }

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
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
        $model = $this->myService->findMyCampaignByKeyOrAbort($id);
        if (count($this->campaignScenarioService->showCampaignScenarioByCampaignUuid($model->uuid))) {
            return $this->sendValidationFailedJsonResponse(["errors" => ["campaign_uuid" => __('messages.campaign_in_scenario')]]);
        }
        $this->service->destroy($id);

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
     * @return JsonResponse
     */
    public function sendEmailsByCampaign(SendEmailByCampaignRequest $request)
    {
        $campaign = $this->service->findOrFailById($request->get('campaign_uuid'));

        $configSmtpAuto = $this->configService->findConfigByKey('smtp_auto');
        //Check random smtp_account with role admin and send_type exists or not
        if ($campaign->send_type != 'sms') {
            if ($campaign->send_type === 'email') {
                $mailMailer = 'smtp';
            } else {
                $mailMailer = $campaign->send_type;
            }

            if ($campaign->user->can_add_smtp_account == 1 || $configSmtpAuto->value == 0) {
                if (!$campaign->smtpAccount && !$this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type)) {
                    return $this->sendValidationFailedJsonResponse([
                        'error' => [
                            'get_random_smtp_account_admin' => __("messages.{$mailMailer}_smtp_account_invalid")
                        ]
                    ]);
                }
            } else {
                if (!$this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type)) {
                    return $this->sendValidationFailedJsonResponse([
                        'error' => [
                            'get_random_smtp_account_admin' => __("messages.{$mailMailer}_smtp_account_invalid")
                        ]
                    ]);
                }
            }
        }

        //Send
        $result = $this->checkAndSendCampaign($request->get('campaign_uuid'));
        if ($result['status']) {
            return $this->sendOkJsonResponse(["message" => $result['messages']]);
        }

        return $this->sendValidationFailedJsonResponse(['errors' => $result['messages']]);
    }

    /**
     * @param SendEmailByMyCampaignRequest $request
     * @return void|JsonResponse
     */
    public function sendEmailByMyCampaign(SendEmailByMyCampaignRequest $request)
    {
        $campaign = $this->service->findOrFailById($request->get('campaign_uuid'));

        $configSmtpAuto = $this->configService->findConfigByKey('smtp_auto');
        //Check random smtp_account with role admin and send_type exists or not
        if ($campaign->send_type != 'sms') {
            if ($campaign->send_type === 'email') {
                $mailMailer = 'smtp';
            } else {
                $mailMailer = $campaign->send_type;
            }

            if ($campaign->user->can_add_smtp_account == 1 || $configSmtpAuto->value == 0) {
                if (!$campaign->smtpAccount && !$this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type)) {
                    return $this->sendValidationFailedJsonResponse([
                        'error' => [
                            'get_random_smtp_account_admin' => __("messages.{$mailMailer}_smtp_account_invalid")
                        ]
                    ]);
                }
            } else {
                if (!$this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type)) {
                    return $this->sendValidationFailedJsonResponse([
                        'error' => [
                            'get_random_smtp_account_admin' => __("messages.{$mailMailer}_smtp_account_invalid")
                        ]
                    ]);
                }
            }
        }

        //Send
        $result = $this->checkAndSendCampaign($request->get('campaign_uuid'));
        if ($result['status']) {
            return $this->sendOkJsonResponse(["message" => $result['messages']]);
        }

        return $this->sendValidationFailedJsonResponse(['errors' => $result['messages']]);
    }

    /**
     * @param StartCampaignRequest $request
     * @return JsonResponse
     */
    public function statusCampaign(StartCampaignRequest $request)
    {
        $campaign = $this->service->findOneById($request->get('campaign_uuid'));
        if ($request->get('was_stopped_by_owner')) {
            $this->service->update($campaign, [
                'was_stopped_by_owner' => $request->get('was_stopped_by_owner')
            ]);
            SendNotificationSystemEvent::dispatch(null, Notification::CAMPAIGN_TYPE, Notification::STOP_ACTION, $campaign);
            return $this->sendOkJsonResponse();
        }

        $this->service->update($campaign, [
            'was_stopped_by_owner' => $request->get('was_stopped_by_owner')
        ]);

        if ($campaign->type != Campaign::CAMPAIGN_BIRTHDAY_TYPE) {
            $result = $this->checkAndSendCampaign($request->get('campaign_uuid'));
            if (!$result['status']) {
                $this->service->update($campaign, [
                    'was_stopped_by_owner' => !$request->get('was_stopped_by_owner')
                ]);

                return $this->sendValidationFailedJsonResponse(['errors' => $result['messages']]);
            }

            return $this->sendOkJsonResponse(["message" => $result['messages']]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param StartMyCampaignRequest $request
     * @return JsonResponse
     */
    public function statusMyCampaign(StartMyCampaignRequest $request)
    {
        $campaign = $this->service->findOneById($request->get('campaign_uuid'));
        if ($request->get('was_stopped_by_owner')) {
            $this->service->update($campaign, [
                'was_stopped_by_owner' => $request->get('was_stopped_by_owner')
            ]);
            SendNotificationSystemEvent::dispatch(null, Notification::CAMPAIGN_TYPE, Notification::STOP_ACTION, $campaign);
            return $this->sendOkJsonResponse();
        }

        $this->service->update($campaign, [
            'was_stopped_by_owner' => $request->get('was_stopped_by_owner')
        ]);

        if ($campaign->type != Campaign::CAMPAIGN_BIRTHDAY_TYPE) {
            $result = $this->checkAndSendCampaign($request->get('campaign_uuid'));
            if (!$result['status']) {
                $this->service->update($campaign, [
                    'was_stopped_by_owner' => !$request->get('was_stopped_by_owner')
                ]);

                return $this->sendValidationFailedJsonResponse(['errors' => $result['messages']]);
            }

            return $this->sendOkJsonResponse(["message" => $result['messages']]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param $campaignUuid
     * @return array
     */
    public function checkAndSendCampaign($campaignUuid)
    {
        $campaign = $this->service->findOneById($campaignUuid);
        $columns = ['type', 'status', 'from_date', 'to_date', 'was_finished', 'was_stopped_by_owner'];
        foreach ($columns as $column) {
            if (!$this->service->checkActiveCampainByColumn($column, $campaignUuid)) {
                return ['status' => false,
                    'messages' => ["campaign_" . $column => __("messages.{$column}_campaign_invalid")]];
            }
        }
        if ($this->sendEmailScheduleLogService->checkActiveCampaignbyCampaignUuid($campaignUuid)) {
            $creditNumberSendEmail = $campaign->number_credit_needed_to_start_campaign;
            if ($this->userProfileService->checkCredit($creditNumberSendEmail, $campaign->user_uuid)) {
                SendNotificationSystemEvent::dispatch(null, Notification::CAMPAIGN_TYPE, Notification::START_ACTION, $campaign);
                SendByCampaignEvent::dispatch($campaign, $creditNumberSendEmail);
                return ['status' => true,
                    'messages' => __('messages.send_campaign_success')];
            }

            return ['status' => false,
                'messages' => ['credit' => __('messages.credit_invalid')]];
        }

        return ['status' => false,
            'messages' => ['campaign_is_running' => __('messages.is_running_campaign_invalid')]];
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
        if ($campaign->send_type == 'email') {
            $config = $this->configService->findConfigByKey('smtp_auto');
            try {
                if ($user->can_add_smtp_account == 1 || $config->value == 0) {
                    if (!empty($campaign->smtpAccount)) {
                        $smtpAccount = $campaign->smtpAccount;
                    } else {
                        $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type);
                    }
                } else {
                    $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type);
                }
                $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccount);
                $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $user->email, $smtpAccount, $campaign);
                Mail::to($user->email)->send(new SendCampaign($mailTemplate, $smtpAccount->mail_from_name, $smtpAccount->mail_from_address, $campaign->reply_to_email, $campaign->reply_name));

                return $this->sendOkJsonResponse(['message' => __('messages.test_send_campaign_success')]);
            } catch (\Exception $e) {

                return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
            }
        } else {
            $contacts = $this->contactService->getContactsSendSms($campaign->uuid);
            $content = $campaign->mailTemplate->body;

            foreach ($contacts as $contact) {
                Log::info('Phone:' . "$contact->phone" . '|' . 'Content:' . "$content");
            }

            return $this->sendOkJsonResponse();
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
        if ($campaign->send_type == 'email') {
            $config = $this->configService->findConfigByKey('smtp_auto');
            try {
                if ($user->can_add_smtp_account == 1 || $config->value == 0) {
                    if (!empty($campaign->smtpAccount)) {
                        $smtpAccount = $campaign->smtpAccount;
                    } else {
                        $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type);
                    }
                } else {
                    $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin($campaign->send_type);
                }
                $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccount);
                $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $user->email, $smtpAccount, $campaign);
                Mail::to($user->email)->send(new SendCampaign($mailTemplate, $smtpAccount->mail_from_name, $smtpAccount->mail_from_address, $campaign->reply_to_email, $campaign->reply_name));

                return $this->sendOkJsonResponse(['message' => __('messages.test_send_campaign_success')]);
            } catch (\Exception $e) {

                return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
            }
        } else {
            $contacts = $this->contactService->getContactsSendSms($campaign->uuid);
            $content = $campaign->mailTemplate->body;

            foreach ($contacts as $contact) {
                Log::info('Phone:' . "$contact->phone" . '|' . 'Content:' . "$content");
            }

            return $this->sendOkJsonResponse();
        }
    }
}
