<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\ImportExcelFileRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Imports\ContactImport;
use App\Services\ContactService;

class ContactController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    public function __construct(ContactService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = ContactResourceCollection::class;
        $this->resourceClass = ContactResource::class;
        $this->storeRequest = ContactRequest::class;
        $this->editRequest = UpdateContactRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

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
}
