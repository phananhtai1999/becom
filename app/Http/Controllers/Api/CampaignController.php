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
use App\Http\Requests\LoadAnalyticDataRequest;
use App\Http\Requests\MyCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Requests\UpdateMyCampaignRequest;
use App\Http\Resources\CampaignDailyTrackingResourceCollection;
use App\Http\Resources\CampaignResourceCollection;
use App\Http\Resources\CampaignDailyTrackingResource;
use App\Http\Resources\CampaignLinkDailyTrackingResource;
use App\Http\Resources\CampaignLinkTrackingResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignTrackingResource;
use App\Services\CampaignDailyTrackingService;
use App\Services\CampaignLinkDailyTrackingService;
use App\Services\CampaignLinkTrackingService;
use App\Services\CampaignService;
use App\Services\CampaignTrackingService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\SendEmailByCampaignService;
use Carbon\Carbon;
use App\Services\MyCampaignService;
use Illuminate\Http\JsonResponse;

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
     * @var EmailService
     */
    protected $emailService;

    /**
     * @var MailSendingHistoryService
     */
    protected $mailSendingHistoryService;

    /**
     * @var SendEmailByCampaignService
     */
    protected $sendEmailByCampaignService;

    /**
     * @param CampaignService $service
     * @param MyCampaignService $myService
     * @param CampaignTrackingService $campaignTrackingService
     * @param CampaignDailyTrackingService $campaignDailyTrackingService
     * @param CampaignLinkDailyTrackingService $campaignLinkDailyTrackingService
     * @param CampaignLinkTrackingService $campaignLinkTrackingService
     */
    public function __construct
    (CampaignService $service,
     MyCampaignService $myService,
     CampaignTrackingService $campaignTrackingService,
     CampaignDailyTrackingService $campaignDailyTrackingService,
     CampaignLinkDailyTrackingService $campaignLinkDailyTrackingService,
     CampaignLinkTrackingService $campaignLinkTrackingService,
     EmailService $emailService,
     MailSendingHistoryService $mailSendingHistoryService,
     SendEmailByCampaignService $sendEmailByCampaignService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = CampaignResourceCollection::class;
        $this->resourceClass = CampaignResource::class;
        $this->storeRequest = CampaignRequest::class;
        $this->editRequest = UpdateCampaignRequest::class;
        $this->campaignTrackingService = $campaignTrackingService;
        $this->campaignDailyTrackingService = $campaignDailyTrackingService;
        $this->campaignLinkTrackingService = $campaignLinkTrackingService;
        $this->campaignLinkDailyTrackingService = $campaignLinkDailyTrackingService;
        $this->emailService = $emailService;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->sendEmailByCampaignService = $sendEmailByCampaignService;
    }

    /**
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyCampaign()
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    request()->get('per_page', '15'),
                    request()->get('page', '1'),
                    request()->get('columns', '*'),
                    request()->get('page_name', 'page')
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

    public function sendEmailsByCampaign()
    {
        $activeCampaign = $this->service->loadActiveCampaign();

        $this->sendEmailByCampaignService->sendEmailByActiveCampaign($activeCampaign);

        return $this->sendOkJsonResponse(["message" => "Send Email By Campaign Success"]);
    }
}
