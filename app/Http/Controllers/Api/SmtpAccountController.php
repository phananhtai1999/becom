<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MySmtpAccountRequest;
use App\Http\Requests\SendMailBySmtpAccountUuidRequest;
use App\Http\Requests\SendMailUseMailTemplateUuidRequest;
use App\Http\Requests\SmtpAccountRequest;
use App\Http\Requests\UpdateMySmtpAccountRequest;
use App\Http\Requests\UpdateSmtpAccountRequest;
use App\Http\Resources\SmtpAccountResourceCollection;
use App\Http\Resources\SmtpAccountResource;
use App\Mail\SendEmails;
use App\Models\MailTemplate;
use App\Services\MySmtpAccountService;
use App\Services\SmtpAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SmtpAccountController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * @var MySmtpAccountService
     */
    protected $myService;

    /**
     * @param SmtpAccountService $service
     * @param MySmtpAccountService $myService
     */
    public function __construct(
        SmtpAccountService $service,
        MySmtpAccountService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = SmtpAccountResourceCollection::class;
        $this->resourceClass = SmtpAccountResource::class;
        $this->storeRequest = SmtpAccountRequest::class;
        $this->editRequest = UpdateSmtpAccountRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function indexMySmtpAccount(IndexRequest $request)
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
     * @param MySmtpAccountRequest $request
     * @return JsonResponse
     */
    public function storeMySmtpAccount(MySmtpAccountRequest $request)
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
    public function showMySmtpAccount($id)
    {
        $model = $this->myService->findMySmtpAccountByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMySmtpAccountRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMySmtpAccount(UpdateMySmtpAccountRequest $request, $id)
    {
        $model = $this->myService->findMySmtpAccountByKeyOrAbort($id);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMySmtpAccount($id)
    {
        $this->myService->deleteMySmtpAccountByKey($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @return JsonResponse|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendEmail(SendMailBySmtpAccountUuidRequest $request)
    {
        try {
            $subject = $request->get('subject');
            $body = $request->get('body');
            $toEmails = $request->get('to_emails');

            $smtpAccount = $this->service->findOneById($request->get('smtp_account_uuid'));
            $this->service->setSmtpAccountForSendEmail($smtpAccount);

            foreach ($toEmails as $emails) {
                Mail::to($emails)->send(new SendEmails($subject, $body));
            }

            return response()->json(['message' => 'Mail Sent Successfully'], 200);
        }catch (\Exception $e){
            return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
        }

    }

    /**
     * @return JsonResponse|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendTemplate(SendMailUseMailTemplateUuidRequest $request)
    {
        try {
            $mailTemplate = MailTemplate::where('uuid', $request->get('mail_template_uuid'))->first();

            $subject = $mailTemplate->subject;
            $body = $mailTemplate->body;
            $toEmails = $request->get('to_emails');

            $smtpAccount = $this->service->findOneById($request->get('smtp_account_uuid'));
            $this->service->setSmtpAccountForSendEmail($smtpAccount);

            foreach ($toEmails as $emails) {
                Mail::to($emails)->send(new SendEmails($subject, $body));
            }

            return response()->json(['message' => 'Mail Sent Successfully'], 200);
        }catch (\Exception $e){
            return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
        }

    }
}
