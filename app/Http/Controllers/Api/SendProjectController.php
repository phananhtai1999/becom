<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MySendProjectRequest;
use App\Http\Requests\UpdateMySendProjectRequest;
use App\Http\Requests\UpdateSendProjectRequest;
use App\Http\Requests\VerifyDomainWebsiteVerificationRequest;
use App\Http\Requests\SendProjectRequest;
use App\Http\Requests\WebsiteVerificationRequest;
use App\Http\Resources\SendProjectResourceCollection;
use App\Http\Resources\SendProjectResource;
use App\Http\Resources\WebsiteVerificationResource;
use App\Services\FileVerificationService;
use App\Services\MySendProjectService;
use App\Services\SendProjectService;
use App\Services\WebsiteVerificationService;
use Illuminate\Http\JsonResponse;

class SendProjectController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MySendProjectService
     */
    protected $myService;

    /**
     * @var WebsiteVerificationService
     */
    protected $websiteVerificationService;

    /**
     * @var FileVerificationService
     */
    protected $fileVerificationService;

    /**
     * @param SendProjectService $service
     * @param WebsiteVerificationService $websiteVerificationService
     * @param FileVerificationService $fileVerificationService
     * @param MySendProjectService $myService
     */
    public function __construct(
        SendProjectService         $service,
        WebsiteVerificationService $websiteVerificationService,
        FileVerificationService    $fileVerificationService,
        MySendProjectService       $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = SendProjectResourceCollection::class;
        $this->resourceClass = SendProjectResource::class;
        $this->storeRequest = SendProjectRequest::class;
        $this->editRequest = UpdateSendProjectRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->websiteVerificationService = $websiteVerificationService;
        $this->fileVerificationService = $fileVerificationService;
    }

    /**
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            "app_id" => auth()->appId(),
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

        if (empty($request->get('user_uuid')) || $model->user_uuid == $request->get('user_uuid')
            || !$this->service->checkExistsWebisteInTables($id)) {

            $data = $request->all();
        } else {
            return $this->sendValidationFailedJsonResponse(["errors" => ["user_uuid" => __('messages.user_uuid_not_changed')]]);
        }

        $this->service->update($model, array_merge($data, [
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            "app_id" => auth()->appId(),
            'domain_uuid' => $request->get('domain_uuid') ?? null
        ]));

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
        if (!$this->service->checkExistsWebisteInTables($id)) {
            $this->service->destroy($id);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);

    }

    /**
     * @param MySendProjectRequest $request
     * @return JsonResponse
     */
    public function storeMySendProject(MySendProjectRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMySendProject($id)
    {
        $model = $this->myService->showMyWebsite($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMySendProjectRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMySendProject(UpdateMySendProjectRequest $request, $id)
    {
        $model = $this->myService->showMyWebsite($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMySendProject($id)
    {
        if (!$this->service->checkExistsWebisteInTables($id)) {
            $this->myService->deleteMyWebsite($id);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);

    }

    /**
     * @param WebsiteVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyByDnsRecord(WebsiteVerificationRequest $request)
    {

        $website = $this->service->findOneWhereOrFail([
            'domain' => $request->get('domain')
        ]);

        $websiteVerify = $this->websiteVerificationService->verifyByDnsRecord($website->getKey());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData(WebsiteVerificationResource::class, $websiteVerify)
        );

    }

    /**
     * @param VerifyDomainWebsiteVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyByHtmlTag(VerifyDomainWebsiteVerificationRequest $request)
    {
        $website = $this->service->findOneWhereOrFail([
            'domain' => $request->get('domain')
        ]);

        $websiteVerify = $this->websiteVerificationService->verifyByHtmlTag($website->getKey());

        $metaTagName = config('app.name') . '-verify-tag';
        $HtmlTag = "<meta name='" . $metaTagName . "' content='" . $websiteVerify->token . "'>";

        return $this->sendOkJsonResponse([
            'data' => [
                'htmlTag' => $HtmlTag,
                'websiteVerify' => $this->service->resourceToData(WebsiteVerificationResource::class, $websiteVerify)['data']
            ]
        ]);

    }

    /**
     * @param VerifyDomainWebsiteVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyByHtmlFile(VerifyDomainWebsiteVerificationRequest $request)
    {
        $website = $this->service->findOneWhereOrFail([
            'domain' => $request->get('domain')
        ]);

        $websiteVerify = $this->websiteVerificationService->verifyByHtmlFile($website->getKey());

        if ($websiteVerify->verified_at) {

            return $this->sendOkJsonResponse(
                $this->service->resourceToData(WebsiteVerificationResource::class, $websiteVerify)
            );
        } else {

            return $this->sendOkJsonResponse([
                'linkDownloadHtmlFile' => route('website.downloadHtmlFile', [$websiteVerify->token])
            ]);
        }
    }

    /**
     * @param $token
     * @return \Illuminate\Http\Response|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function downloadHtmlFile($token)
    {
        $verificationFileName = $this->fileVerificationService->verificationFileName();
        $contentFile = $token;
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $verificationFileName),
            'Content-Length' => strlen($contentFile),
        ];

        return response()->make($contentFile, 200, $headers);
    }
}
