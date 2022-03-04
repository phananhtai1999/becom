<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\SendMailBySmtpAccountUuidRequest;
use App\Http\Requests\SendMailUseMailTemplateUuidRequest;
use App\Http\Requests\SmtpAccountRequest;
use App\Http\Requests\UpdateSmtpAccountRequest;
use App\Http\Resources\SmtpAccountCollection;
use App\Http\Resources\SmtpAccountResource;
use App\Mail\SendEmails;
use App\Models\MailTemplate;
use App\Models\SmtpAccount;
use App\Services\SmtpAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SmtpAccountController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    public function __construct(SmtpAccountService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = SmtpAccountCollection::class;
        $this->resourceClass = SmtpAccountResource::class;
        $this->storeRequest = SmtpAccountRequest::class;
        $this->editRequest = UpdateSmtpAccountRequest::class;
    }

    /**
     * @return JsonResponse|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendEmail(SendMailBySmtpAccountUuidRequest $request)
    {
        $subject = $request->get('subject');
        $body = $request->get('body');
        $toEmails = $request->get('to_emails');

        $this->service->sendEmailsBySmtpAccount();

        foreach ($toEmails as $emails) {
            Mail::to($emails)->send(new SendEmails($subject, $body));
        }

        return response()->json(['message' => 'Mail Sent Successfully'], 200);
    }

    /**
     * @return JsonResponse|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendTemplate(SendMailUseMailTemplateUuidRequest $request)
    {
        $mailTemplate = MailTemplate::where('uuid', request()->get('mail_template_uuid'))->first();

        $subject = $mailTemplate->subject;
        $body = $mailTemplate->body;
        $toEmails = $request->get('to_emails');

        $this->service->sendEmailsBySmtpAccount();

        foreach ($toEmails as $emails) {
            Mail::to($emails)->send(new SendEmails($subject, $body));
        }

        return response()->json(['message' => 'Mail Sent Successfully'], 200);
    }
}
