<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyWebsiteRequest;
use App\Http\Requests\UpdateMyWebsiteRequest;
use App\Http\Requests\UpdateWebsiteRequest;
use App\Http\Requests\VerifyDomainWebsiteVerificationRequest;
use App\Http\Requests\WebsiteRequest;
use App\Http\Requests\WebsiteVerificationRequest;
use App\Http\Resources\WebsiteResourceCollection;
use App\Http\Resources\WebsiteResource;
use App\Http\Resources\WebsiteVerificationResource;
use App\Services\FileVerificationService;
use App\Services\MyWebsiteService;
use App\Services\WebsiteService;
use App\Services\WebsiteVerificationService;

class WebsiteController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    /**
     * @var MyWebsiteService
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
     * @param WebsiteService $service
     * @param WebsiteVerificationService $websiteVerificationService
     * @param FileVerificationService $fileVerificationService
     * @param MyWebsiteService $myService
     */
    public function __construct(
        WebsiteService $service,
        WebsiteVerificationService $websiteVerificationService,
        FileVerificationService $fileVerificationService,
        MyWebsiteService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = WebsiteResourceCollection::class;
        $this->resourceClass = WebsiteResource::class;
        $this->storeRequest = WebsiteRequest::class;
        $this->editRequest = UpdateWebsiteRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->websiteVerificationService = $websiteVerificationService;
        $this->fileVerificationService = $fileVerificationService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if($request->has('user_uuid')){
            $data = $request->all();
        }else{
            $data = array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
            ]);
        }
        $model = $this->service->create($data);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyWebsite(IndexRequest $request)
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
     * @param MyWebsiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyWebsite(MyWebsiteRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyWebsite($id)
    {
        $model = $this->myService->showMyWebsite($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyWebsiteRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyWebsite(UpdateMyWebsiteRequest $request, $id)
    {
        $model = $this->myService->showMyWebsite($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyWebsite($id)
    {
        $this->myService->deleteMyWebsite($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param WebsiteVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
