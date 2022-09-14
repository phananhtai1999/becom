<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\ImportExcelFileRequest;
use App\Http\Requests\ImportJsonFileRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Imports\ContactImport;
use App\Services\ContactService;

class ContactController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    public function __construct(ContactService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = ContactResourceCollection::class;
        $this->resourceClass = ContactResource::class;
        $this->storeRequest = ContactRequest::class;
        $this->editRequest = UpdateContactRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create($request->all());

        $model->contactLists()->attach($request->get('contact_list', []));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        $contactListUuid = $this->service->findContactListKeyByContact($model);

        if ($contactListUuid == null) {
            $model->contactLists()->sync($request->get('contact_list', []));
        } else {
            $model->contactLists()->sync($request->get('contact_list', $contactListUuid));
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param ImportExcelFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importExcelFile(ImportExcelFileRequest $request)
    {
        try {
            $import = new ContactImport();
            $import->import($request->file);

            if ($import->failures()->isNotEmpty()) {
                foreach ($import->failures() as $failure) {

                    return $this->sendValidationFailedJsonResponse([
                        'errors' => [
                            $failure->attribute() => $failure->errors()
                        ]
                    ]);
                }
            }

            return $this->sendOkJsonResponse();
        } catch (\ErrorException $errorException) {

            return $this->sendValidationFailedJsonResponse();
        } catch (\TypeError $typeError) {

            return $this->sendValidationFailedJsonResponse([
                'errors' => [
                    'dob' => [
                        __('messages.date_format')
                    ]
                ]
            ]);
        }
    }

    /**
     * @param ImportJsonFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importJsonFile(ImportJsonFileRequest $request)
    {
        $this->service->importJsonFile($request->file);

        return $this->sendOkJsonResponse();
    }
}
