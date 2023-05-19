<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\Purpose\ChangeStatusPurposeRequest;
use App\Http\Requests\Purpose\DestroyPurposeRequest;
use App\Http\Requests\Purpose\PurposeRequest;
use App\Http\Requests\Purpose\UpdatePurposeRequest;
use App\Http\Resources\PurposeResource;
use App\Http\Resources\PurposeResourceCollection;
use App\Models\Purpose;
use App\Services\LanguageService;
use App\Services\MailTemplateService;
use App\Services\PurposeService;
use Illuminate\Http\JsonResponse;

class PurposeController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    protected $mailTemplateService;

    /**
     * @param PurposeService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        PurposeService $service,
        LanguageService $languageService,
        MailTemplateService $mailTemplateService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PurposeResourceCollection::class;
        $this->resourceClass = PurposeResource::class;
        $this->storeRequest = PurposeRequest::class;
        $this->editRequest = UpdatePurposeRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->languageService = $languageService;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (!$this->languageService->checkLanguages($request->title)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create($request->except('publish_status'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        if ($request->title && !$this->languageService->checkLanguages($request->title)) {
            return $this->sendValidationFailedJsonResponse();
        }
        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function indexPublic(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => Purpose::PUBLISHED_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function changeStatus($id, ChangeStatusPurposeRequest $request)
    {
        $purpose = $this->service->findOneById($id);
        $status = $request->get('publish_status');

        $goPurposeUuid = $request->get('purpose_uuid');
        if ($status == Purpose::PENDING_PUBLISH_STATUS){
            $this->mailTemplateService->movePurposeOfMailTemplate($id, $goPurposeUuid);
        }

        $this->service->update($purpose, [
            'publish_status' => $status
        ]);
        return $this->sendOkJsonResponse();
    }

    public function destroyPurpose($id, DestroyPurposeRequest $request)
    {
        $purpose = $this->service->findOneById($id);

        $goPurposeUuid = $request->get('purpose_uuid');

        $this->mailTemplateService->movePurposeOfMailTemplate($id, $goPurposeUuid);

        $purpose->delete();

        return $this->sendOkJsonResponse();
    }
}
