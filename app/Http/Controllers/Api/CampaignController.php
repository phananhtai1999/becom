<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendEmailByCampaignEvent;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
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
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailByCampaignService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use Carbon\Carbon;
use App\Services\MyCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class CampaignController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

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
        MailSendingHistoryService $mailSendingHistoryService
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
        $model = $this->service->create($request->all());

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

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyCampaignRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMyCampaign(UpdateMyCampaignRequest $request, $id)
    {
        $model = $this->myService->findMyCampaignByKeyOrAbort($id);

        $this->service->update($model, $request->all());

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
     * @return JsonResponse
     */
    public function sendEmailsByCampaign(SendEmailByCampaignRequest $request)
    {
        $campaign = $this->service->findOneById($request->get('campaign_uuid'));
        if($request->get('is_save_history')){
            if($this->sendEmailScheduleLogService->checkActiveCampaignbyCampaignUuid($request->get('campaign_uuid'))){
                if($this->emailService->checkEmailValid($request->get('to_emails'), $campaign->website_uuid)){
                    if($this->mailSendingHistoryService->checkTodayNumberEmailSentUser($campaign, $request->get('to_emails'))){
                        SendEmailByCampaignEvent::dispatch($campaign, $request->get('to_emails'));

                        return $this->sendOkJsonResponse(["message" => "Send Email By Campaign Success"]);
                    }

                    return $this->sendValidationFailedJsonResponse(["errors" => ['to_emails' => 'There were emails that received the campaign today']]);
                }

                return $this->sendValidationFailedJsonResponse(["errors" => ['to_emails' => 'The selected to emails is invalid']]);
            }

            return $this->sendValidationFailedJsonResponse(["errors" => ['campaign_uuid' => 'The selected campaign uuid is invalid']]);

        }else{
            try {
                $this->smtpAccountService->setSmtpAccountForSendEmail($campaign->smtpAccount);
                foreach ($request->get('to_emails') as $email){
                    $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $email, $campaign->smtpAccount, $campaign);
                    Mail::to($email)->send(new SendCampaign($mailTemplate));
                }

                return $this->sendOkJsonResponse(["message" => "Test Send Email By Campaign Success"]);
            }catch (\Exception $e){
                return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
            }
        }
    }

    /**
     * @param SendEmailByMyCampaignRequest $request
     * @return JsonResponse
     */
    public function sendEmailByMyCampaign(SendEmailByMyCampaignRequest $request)
    {
        if($this->myService->CheckMyCampaign($request->get('campaign_uuid'))){
            $campaign = $this->service->findOneById($request->get('campaign_uuid'));
            if($request->get('is_save_history')){
                if($this->sendEmailScheduleLogService->checkActiveCampaignbyCampaignUuid($request->get('campaign_uuid'))){
                    if($this->emailService->checkEmailValid($request->get('to_emails'), $campaign->website_uuid)){
                        if($this->mailSendingHistoryService->checkTodayNumberEmailSentUser($campaign, $request->get('to_emails'))){
                            SendEmailByCampaignEvent::dispatch($campaign, $request->get('to_emails'));

                            return $this->sendOkJsonResponse(["message" => "Send Email By Campaign Success"]);
                        }

                        return $this->sendValidationFailedJsonResponse(["errors" => ['to_emails' => 'There were emails that received the campaign today']]);
                    }

                    return $this->sendValidationFailedJsonResponse(["errors" => ['to_emails' => 'The selected to emails is invalid']]);
                }

                return $this->sendValidationFailedJsonResponse(["errors" => ['campaign_uuid' => 'The selected campaign uuid is invalid']]);

            }else{
                try {
                    $this->smtpAccountService->setSmtpAccountForSendEmail($campaign->smtpAccount);
                    foreach ($request->get('to_emails') as $email){
                        $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $email, $campaign->smtpAccount, $campaign);
                        Mail::to($email)->send(new SendCampaign($mailTemplate));
                    }

                    return $this->sendOkJsonResponse(["message" => "Test Send Email By Campaign Success"]);
                }catch (\Exception $e){
                    return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
                }
            }
        }
        return $this->sendValidationFailedJsonResponse(["errors" => ['campaign_uuid' => 'The selected campaign uuid is invalid']]);
    }
}
