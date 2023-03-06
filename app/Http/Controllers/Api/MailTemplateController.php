<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AcceptPublishMailTemplateRequest;
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
use App\Services\WebsiteService;
use Illuminate\Http\JsonResponse;

class MailTemplateController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MyMailTemplateService
     */
    protected $myService;

    /**
     * @var WebsiteService
     */
    protected $websiteService;

    /**
     * @param MailTemplateService $service
     * @param MyMailTemplateService $myService
     * @param WebsiteService $websiteService
     */
    public function __construct(
        MailTemplateService $service,
        MyMailTemplateService $myService,
        WebsiteService $websiteService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = MailTemplateResourceCollection::class;
        $this->resourceClass = MailTemplateResource::class;
        $this->storeRequest = MailTemplateRequest::class;
        $this->editRequest = UpdateMailTemplateRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->websiteService = $websiteService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (empty($request->get('website_uuid'))) {
            $userUuid = auth()->user()->getKey();
        }else {
            $website = $this->websiteService->findOneById($request->get('website_uuid'));
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

        if (empty($request->get('website_uuid')) || $model->website_uuid == $request->get('website_uuid')) {
            $data = $request->except('user_uuid');
        }else {
            if (!$this->service->checkExistsMailTemplateInTables($id)) {
                $website = $this->websiteService->findOneById($request->get('website_uuid'));
                $data = array_merge($request->all(), [
                    'user_uuid' => $website->user_uuid,
                ]);
            } else {
                return $this->sendValidationFailedJsonResponse(["errors" => ["website_uuid" => __('messages.website_uuid_not_changed')]]);
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
            'user_uuid' => auth()->user()->getkey(),
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

        if (empty($request->get('website_uuid')) || $model->website_uuid == $request->get('website_uuid') ||
            !$this->service->checkExistsMailTemplateInTables($id)) {

            $this->service->update($model, $request->except(['user_uuid', 'publish_status']));

            return $this->sendOkJsonResponse(
                $this->service->resourceToData($this->resourceClass, $model)
            );
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["website_uuid" => __('messages.website_uuid_not_changed')]]);
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
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->service->indexMailtemplateByPublishStatus(
                    MailTemplate::PENDING_PUBLISH_STATUS,
                    $request->get('per_page', '15'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
                    $request->get('page', '1'),
                    $request->get('search'),
                    $request->get('search_by'),
                )
            )
        );
    }

    /**
     * @param UnpublishedMailTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUnpublishedMailTemplate(UnpublishedMailTemplateRequest $request)
    {
        if (empty($request->get('website_uuid'))) {
            $userUuid = auth()->user()->getKey();
        }else {
            $website = $this->websiteService->findOneById($request->get('website_uuid'));
            $userUuid = $website->user_uuid;
        }

        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => MailTemplate::PENDING_PUBLISH_STATUS,
            'user_uuid' => $userUuid
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
        $model = $this->service->findMailTemplateByKeyAndPublishStatus(MailTemplate::PENDING_PUBLISH_STATUS, $id);

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
        $model = $this->service->findMailTemplateByKeyAndPublishStatus(MailTemplate::PENDING_PUBLISH_STATUS, $id);

        if (empty($request->get('website_uuid')) || $model->website_uuid == $request->get('website_uuid')) {
            $data = $request->except('user_uuid');
        }else {
            if (!$this->service->checkExistsMailTemplateInTables($id)) {
                $website = $this->websiteService->findOneById($request->get('website_uuid'));
                $data = array_merge($request->all(), [
                    'user_uuid' => $website->user_uuid,
                ]);
            } else {
                return $this->sendValidationFailedJsonResponse(["errors" => ["website_uuid" => __('messages.website_uuid_not_changed')]]);
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
    public function acceptPublishMailtemplate(AcceptPublishMailTemplateRequest $request)
    {
        $mailTemplateUuids = $request->mail_templates;
        foreach ($mailTemplateUuids as $mailTemplateUuid)
        {
            $model = $this->service->findOneById($mailTemplateUuid);
            $this->service->update($model, ['publish_status' => MailTemplate::PUBLISHED_PUBLISH_STATUS]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMailTemplatesDefault(IndexRequest $request)
    {
        $models = $this->service->getMailTemplateDefaultWithPagination(
            MailTemplate::PUBLISHED_PUBLISH_STATUS,
            $request->get('per_page', '15'),
            $request->get('page', '1'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
        );

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
