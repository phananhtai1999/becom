<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AcceptPublishMailTemplateRequest;
use App\Http\Requests\EditorMailTemplateChartRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MailTemplateRequest;
use App\Http\Requests\MyMailTemplateRequest;
use App\Http\Requests\UnpublishedMailTemplateRequest;
use App\Http\Requests\UpdateMailTemplateRequest;
use App\Http\Requests\UpdateMyMailTemplateRequest;
use App\Http\Requests\UpdateUnpublishedMailTemplateRequest;
use App\Http\Resources\MailTemplateResourceCollection;
use App\Http\Resources\MailTemplateResource;
use App\Models\MailTemplate;
use App\Services\MailTemplateService;
use App\Services\MyMailTemplateService;
use App\Services\SendProjectService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MailTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MyMailTemplateService
     */
    protected $myService;

    /**
     * @var SendProjectService
     */
    protected $sendProjectService;

    /**
     * @param MailTemplateService $service
     * @param MyMailTemplateService $myService
     * @param SendProjectService $sendProjectService
     */
    public function __construct(
        MailTemplateService   $service,
        MyMailTemplateService $myService,
        SendProjectService    $sendProjectService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = MailTemplateResourceCollection::class;
        $this->resourceClass = MailTemplateResource::class;
        $this->storeRequest = MailTemplateRequest::class;
        $this->editRequest = UpdateMailTemplateRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->sendProjectService = $sendProjectService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (empty($request->get('send_project_uuid'))) {
            $userUuid = auth()->userId();
        } else {
            $website = $this->sendProjectService->findOneById($request->get('send_project_uuid'));
            $userUuid = $website->user_uuid;
        }
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $userUuid,
        ]));

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

        if (empty($request->get('send_project_uuid')) || $model->send_project_uuid == $request->get('send_project_uuid')) {
            $data = $request->except('user_uuid');
        } else {
            if (!$this->service->checkExistsMailTemplateInTables($id)) {
                $website = $this->sendProjectService->findOneById($request->get('send_project_uuid'));
                $data = array_merge($request->all(), [
                    'user_uuid' => $website->user_uuid,
                ]);
            } else {
                return $this->sendValidationFailedJsonResponse(["errors" => ["send_project_uuid" => __('messages.website_uuid_not_changed')]]);
            }
        }
        $this->service->update($model, $data);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        if (!$this->service->checkExistsMailTemplateInTables($id)) {
            $this->service->destroy($id);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);
    }

    /**
     * @param MyMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyMailTemplate(MyMailTemplateRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'publish_status' => MailTemplate::PUBLISHED_PUBLISH_STATUS
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyMailTemplate($id)
    {
        $model = $this->myService->findMyMailTemplateByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyMailTemplateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyMailTemplate(UpdateMyMailTemplateRequest $request, $id)
    {
        $model = $this->myService->findMyMailTemplateByKeyOrAbort($id);

        if (empty($request->get('send_project_uuid')) || $model->send_project_uuid == $request->get('send_project_uuid') ||
            !$this->service->checkExistsMailTemplateInTables($id)) {

            $this->service->update($model, $request->except(['user_uuid', 'publish_status']));

            return $this->sendOkJsonResponse(
                $this->service->resourceToData($this->resourceClass, $model)
            );
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["send_project_uuid" => __('messages.website_uuid_not_changed')]]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyMailTemplate($id)
    {
        if (!$this->service->checkExistsMailTemplateInTables($id)) {
            $this->myService->deleteMyMailTemplateByKey($id);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexUnpublishedMailTemplate(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => MailTemplate::PENDING_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param UnpublishedMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUnpublishedMailTemplate(UnpublishedMailTemplateRequest $request)
    {
        if (empty($request->get('send_project_uuid'))) {
            $userUuid = auth()->userId();
        } else {
            $website = $this->sendProjectService->findOneById($request->get('send_project_uuid'));
            $userUuid = $website->user_uuid;
        }

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $userUuid,
            'app_id' => auth()->appId()
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUnpublishedMailTemplate($id)
    {
        $model = $this->service->showMailTemplateForEditorById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateUnpublishedMailTemplateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editUnpublishedMailTemplate(UpdateUnpublishedMailTemplateRequest $request, $id)
    {
        $model = $this->service->showMailTemplateForEditorById($id);

        if (empty($request->get('send_project_uuid')) || $model->send_project_uuid == $request->get('send_project_uuid')) {
            $data = $request->except('user_uuid');
        } else {
            if (!$this->service->checkExistsMailTemplateInTables($id)) {
                $website = $this->sendProjectService->findOneById($request->get('send_project_uuid'));
                $data = array_merge($request->all(), [
                    'user_uuid' => $website->user_uuid,
                ]);
            } else {
                return $this->sendValidationFailedJsonResponse(["errors" => ["send_project_uuid" => __('messages.website_uuid_not_changed')]]);
            }
        }

        $this->service->update($model, array_merge($data, [
            'publish_status' => 2,
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param AcceptPublishMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatusMailtemplate(AcceptPublishMailTemplateRequest $request)
    {
        $mailTemplateUuids = $request->mail_templates;
        foreach ($mailTemplateUuids as $mailTemplateUuid) {
            $model = $this->service->findOneById($mailTemplateUuid);
            $list_reason = $model->reject_reason;
            if ($request->get('publish_status') == MailTemplate::REJECT_PUBLISH_STATUS) {
                $list_reason[] = [
                    'content' => $request->get('reject_reason'),
                    'created_at' => Carbon::now()
                ];
            }
            $this->service->update($model, [
                'publish_status' => $request->get('publish_status'),
                'reject_reason' => $list_reason
            ]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMailTemplatesDefault(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['publish_status', MailTemplate::PUBLISHED_PUBLISH_STATUS],
            ['send_project_uuid', null],
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function editorMailTemplateChart(EditorMailTemplateChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');
        $type = $request->get('type');

        $data = $this->service->editorMailTemplateChart($groupBy, $startDate, $endDate, $type);
        $total = $this->service->totalEditorMailTemplateChart($startDate, $endDate, $type);

        return $this->sendOkJsonResponse([
            'data' => $data,
            'total' => $total
        ]);
    }
}
