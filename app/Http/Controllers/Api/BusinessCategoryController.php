<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\Business\BusinessCategoryRequest;
use App\Http\Requests\Business\ChangeStatusBusinessCategoryRequest;
use App\Http\Requests\Business\DestroyBusinessCategoryRequest;
use App\Http\Requests\Business\UpdateBusinessCategoryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\BusinessCategoryResource;
use App\Http\Resources\BusinessCategoryResourceCollection;
use App\Models\BusinessCategory;
use App\Services\BusinessCategoryService;
use Techup\ApiConfig\Services\LanguageService;
use App\Services\MailTemplateService;
use Illuminate\Http\JsonResponse;

class BusinessCategoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var LanguageService
     */
    protected $languageService;

    protected $mailTemplateService;

    /**
     * @param BusinessCategoryService $service
     * @param LanguageService $languageService
     */
    public function __construct(
        BusinessCategoryService $service,
        LanguageService $languageService,
        MailTemplateService $mailTemplateService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = BusinessCategoryResourceCollection::class;
        $this->resourceClass = BusinessCategoryResource::class;
        $this->storeRequest = BusinessCategoryRequest::class;
        $this->editRequest = UpdateBusinessCategoryRequest::class;
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

        $this->service->update($model, $request->except('publish_status'));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function indexPublic(IndexRequest $request)
    {
        $models = $this->service->getBusinessCategoriesPublicWithPagination(
            $request->get('per_page', '15'),
            $request->get('page', '1'),
            $request->get('columns', '*'),
            $request->get('page_name', 'page'),
            $request->get('search'),
            $request->get('search_by'),
        );

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showPublic($id)
    {
        $model = $this->service->showBusinessCategoryPublic($id);
        if ($model) {
            return $this->sendOkJsonResponse(
                $this->service->resourceToData($this->resourceClass, $model)
            );
        }
        return $this->sendValidationFailedJsonResponse();

    }

    public function changeStatus($id, ChangeStatusBusinessCategoryRequest $request)
    {
        $businessCategory = $this->service->findOrFailById($id);
        $status = $request->get('publish_status');

        if ($status == BusinessCategory::PENDING_PUBLISH_STATUS){
            $goCatUuid = $request->get('business_category_uuid');

            //Chuyển Cat Cha -> pending thì Cat con cũng k show => cũng phải move templates của Cat con
            $catsChildAndSelf = $businessCategory->getDescendantsAndSelf()->pluck('uuid');
            $mailTemplates = $this->mailTemplateService->findAllWhereIn('business_category_uuid', $catsChildAndSelf, ['uuid', 'business_category_uuid']);
            if (($mailTemplates->count() > 0 && !$goCatUuid) || (in_array($goCatUuid, $catsChildAndSelf->toArray()))) {
                return $this->sendValidationFailedJsonResponse(["errors" => ["business_category_uuid" => "The selected business category uuid is invalid"]]);
            }

            //Lấy tất cả Mail template có Cat con và Chính nó và Update lại Cat
            $this->mailTemplateService->moveBusinessCategoryOfMailTemplates($mailTemplates, $goCatUuid);
        }

        $this->service->update($businessCategory, [
           'publish_status' => $status
        ]);

        return $this->sendOkJsonResponse();
    }

    public function destroyBusinessCategory($id, DestroyBusinessCategoryRequest $request)
    {
        $businessCategory = $this->service->findOrFailById($id);
        $catsChildAndSelf = $businessCategory->getDescendantsAndSelf()->pluck('uuid');

        $goCatUuid = $request->get('business_category_uuid');
        $mailTemplates = $this->mailTemplateService->findAllWhereIn('business_category_uuid', $catsChildAndSelf, ['uuid', 'business_category_uuid']);
        if (($mailTemplates->count() > 0 && !$goCatUuid) || (in_array($goCatUuid, $catsChildAndSelf->toArray()))) {
            return $this->sendValidationFailedJsonResponse(["errors" => ["business_category_uuid" => "The selected business category uuid is invalid"]]);
        }
        $this->mailTemplateService->moveBusinessCategoryOfMailTemplates($mailTemplates, $goCatUuid);
        $this->service->destroy($id);

        return $this->sendOkJsonResponse();
    }
}
