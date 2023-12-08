<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Requests\ChartRequest;
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
use App\Services\ConfigService;
use App\Services\MySmtpAccountService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use App\Services\SendProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SmtpAccountController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MySmtpAccountService
     */
    protected $myService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var SendProjectService
     */
    protected $sendProjectService;

    /**
     * @param SmtpAccountService $service
     * @param MySmtpAccountService $myService
     * @param UserService $userService
     * @param ConfigService $configService
     * @param SendProjectService $sendProjectService
     */
    public function __construct(
        SmtpAccountService   $service,
        MySmtpAccountService $myService,
        UserService          $userService,
        ConfigService        $configService,
        SendProjectService   $sendProjectService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->userService = $userService;
        $this->configService = $configService;
        $this->resourceCollectionClass = SmtpAccountResourceCollection::class;
        $this->resourceClass = SmtpAccountResource::class;
        $this->storeRequest = SmtpAccountRequest::class;
        $this->editRequest = UpdateSmtpAccountRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->sendProjectService = $sendProjectService;
    }

    /**
     * @return JsonResponse
     */
    public function indexWithoutDefault(IndexRequest $request)
    {
        $models = $this->service->getAllSmtpAccountWithoutDefault(
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
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if ($request->get('send_project_uuid')) {
            $website = $this->sendProjectService->findOneById($request->get('send_project_uuid'));
            $userUuid = $website->user_uuid;
        } else {
            $userUuid = auth()->user();
        }

        if (!$this->service->checkMailUserNameUnique($request->get('mail_username'), $userUuid)) {
            return $this->sendValidationFailedJsonResponse(["errors" => ["mail_username" => __('messages.mail_username_already_taken')]]);
        }

        $model = $this->service->create(array_merge($request->except(['status', 'publish']), [
            'user_uuid' => $userUuid,
            'app_id' => auth()->appId(),
        ]));

        //Test smtp account khi tạo 1 smtp
        if (!$this->service->testSmtpAccount($model)) {
            $this->service->update($model, [
                'status' => 'error',
                'publish' => false
            ]);
        }
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

        $model = $this->service->findOrFailById($id);
        if (empty($request->get('send_project_uuid')) || $model->send_project_uuid == $request->get('send_project_uuid')) {
            if ($request->get('mail_username') && $model->mail_username != $request->get('mail_username')
                && !$this->service->checkMailUserNameUnique($request->get('mail_username'), $model->user_uuid)) {
                return $this->sendValidationFailedJsonResponse(["errors" => ["mail_username" => __('messages.mail_username_already_taken')]]);
            }
            $data = $request->except(['status', 'user_uuid']);
        } else {
            if (!$this->service->checkExistsSmtpAccountInTables($id)) {
                $website = $this->sendProjectService->findOneById($request->get('send_project_uuid'));
                if (!$this->service->checkMailUserNameUnique($request->get('mail_username'), $website->user_uuid)) {
                    return $this->sendValidationFailedJsonResponse(["errors" => ["mail_username" => __('messages.mail_username_already_taken')]]);
                }
                $data = array_merge($request->except('status'), [
                    'user_uuid' => $website->user_uuid,
                ]);
            } else {
                return $this->sendValidationFailedJsonResponse(["errors" => ["send_project_uuid" => __('messages.website_uuid_not_changed')]]);
            }
        }
        $this->service->update($model, $data);

        //Test smtp khi update lại smtp
        if ($request->hasAny(['mail_mailer', 'mail_host', 'mail_port',
            'mail_username', 'mail_password', 'smtp_mail_encryption_uuid'])) {
            if (!$this->service->testSmtpAccount($model)) {
                $this->service->update($model, [
                    'status' => 'error',
                    'publish' => false
                ]);
            } else {
                $this->service->update($model, [
                    'status' => 'work',
                ]);
            }
        }

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
        if (!$this->service->checkExistsSmtpAccountInTables($id)) {
            $this->service->destroy($id);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);

    }

    /**
     * @param MySmtpAccountRequest $request
     * @return JsonResponse
     */
    public function storeMySmtpAccount(MySmtpAccountRequest $request)
    {
        $user = $this->userService->findOrFailById(auth()->user());
        $config = $this->configService->findConfigByKey('smtp_auto');

        if ($user->can_add_smtp_account == 1 || $config->value == 0) {
            $model = $this->service->create(array_merge($request->except(['status', 'publish']), [
                'user_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
            ]));

            //Test smtp account khi tạo 1 smtp
            if (!$this->service->testSmtpAccount($model)) {
                $this->service->update($model, [
                    'status' => 'error',
                    'publish' => false
                ]);
            }

            return $this->sendCreatedJsonResponse(
                $this->service->resourceToData($this->resourceClass, $model)
            );
        } else {
            return $this->sendUnAuthorizedJsonResponse();
        }
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
        $config = $this->configService->findConfigByKey('smtp_auto');

        if ($model->user->can_add_smtp_account == 1 || $config->value == 0) {
            if (empty($request->get('send_project_uuid')) || $model->send_project_uuid == $request->get('send_project_uuid') ||
                !$this->service->checkExistsSmtpAccountInTables($id)) {

                $data = $request->except(['user_uuid', 'status', 'publish']);
            } else {
                return $this->sendValidationFailedJsonResponse(["errors" => ["send_project_uuid" => __('messages.website_uuid_not_changed')]]);
            }

            $this->service->update($model, $data);

            //Test smtp khi update lại smtp
            if ($request->hasAny(['mail_mailer', 'mail_host', 'mail_port',
                'mail_username', 'mail_password', 'smtp_mail_encryption_uuid'])) {
                if (!$this->service->testSmtpAccount($model)) {
                    $this->service->update($model, [
                        'status' => 'error',
                        'publish' => false
                    ]);
                } else {
                    $this->service->update($model, [
                        'status' => 'work',
                        'publish' => true
                    ]);
                }
            }

            return $this->sendOkJsonResponse(
                $this->service->resourceToData($this->resourceClass, $model)
            );
        } else {
            return $this->sendUnAuthorizedJsonResponse();
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMySmtpAccount($id)
    {
        if (!$this->service->checkExistsSmtpAccountInTables($id)) {
            $this->myService->deleteMySmtpAccountByKey($id);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);
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

            $smtpAccount = $this->service->findOneById($request->get('smtp_account_uuid'));
            $this->service->setSmtpAccountForSendEmail($smtpAccount);

            Mail::to(auth()->user()->email)->send(new SendEmails($subject, $body));

            return $this->sendOkJsonResponse(['message' => __('messages.sent_mail_success')]);
        } catch (\Exception $e) {
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

            $smtpAccount = $this->service->findOneById($request->get('smtp_account_uuid'));
            $this->service->setSmtpAccountForSendEmail($smtpAccount);

            Mail::to(auth()->user()->email)->send(new SendEmails($subject, $body));

            return $this->sendOkJsonResponse(['message' => __('messages.sent_mail_success')]);
        } catch (\Exception $e) {
            return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
        }

    }

    /**
     * @param ChartRequest $request
     * @return JsonResponse
     */
    public function smtpAccountChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');
        $data = $this->service->smtpAccountChart($groupBy, $startDate, $endDate);
        $total = $this->service->totalActiveAndInactiveSmtpAccountChart($startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $data,
            'total' => $total['0']
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getMailMailerSmtpAccount()
    {
        return $this->sendOkJsonResponse([
            'data' => config('mailmailer')
        ]);
    }

    /***
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function getDefault(IndexRequest $request)
    {
        $models = $this->service->getSmtpAccountDefaultWithPagination(
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
}
