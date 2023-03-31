<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\UnsubscribeRequest;
use App\Http\Resources\UnsubscribeResource;
use App\Services\ContactService;
use App\Services\ContactUnsubscribeService;
use App\Services\UnsubscribeService;
use Illuminate\Http\Request;

class UnsubscribeController extends AbstractRestAPIController
{
    use RestShowTrait;

    protected $contactService;

    protected $contactUnsubscribeService;

    public function __construct(
        UnsubscribeService $service,
        ContactService $contactService,
        ContactUnsubscribeService $contactUnsubscribeService
    )
    {
        $this->service = $service;
        $this->resourceClass = UnsubscribeResource::class;
        $this->contactService = $contactService;
        $this->contactUnsubscribeService = $contactUnsubscribeService;
    }

    /**
     * @param UnsubscribeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUnsubscribe(UnsubscribeRequest $request)
    {
        $codeUnsubscribe = $this->service->findOrFailById($request->get('code'));
        $contact = $this->contactService->findOrFailById($codeUnsubscribe->contact_uuid);

        //Xử lý lưu vào contact_business_category
        if ($request->get('business_categories')) {
            $contact->businessCategories()->sync($request->get('business_categories'));
        }

        //Xử lý lưu vào contact_unsubscribes
        if ($unsubscribes = $request->get('unsubscribes')) {
            $this->contactUnsubscribeService->handleContactUnsubscribeByContact($contact, $unsubscribes);
        }

        //Xóa record unsubscribe đó
        $codeUnsubscribe->delete();

        return $this->sendOkJsonResponse();
    }
}
