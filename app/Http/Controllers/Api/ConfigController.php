<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ConfigRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateCachePlatformConfig;
use App\Http\Requests\UpdateConfigRequest;
use App\Http\Requests\UpsertConfigRequest;
use App\Http\Resources\ConfigResourceCollection;
use App\Http\Resources\ConfigResource;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Mail\SendEmails;
use Techup\ApiConfig\Models\Config;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\LanguageService;
use App\Services\SmtpAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ConfigController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait;

    protected $smtpAccountService;
    protected $languageService;

    /**
     * @param ConfigService $service
     * @param SmtpAccountService $smtpAccountService
     * @param LanguageService $languageService
     */
    public function __construct(
        ConfigService      $service,
        SmtpAccountService $smtpAccountService,
        LanguageService    $languageService
    )
    {
        $this->service = $service;
        $this->languageService = $languageService;
        $this->resourceCollectionClass = ConfigResourceCollection::class;
        $this->resourceClass = ConfigResource::class;
        $this->storeRequest = ConfigRequest::class;
        $this->editRequest = UpdateConfigRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->smtpAccountService = $smtpAccountService;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        //Check type is meta_tag with multi lang for title,des,keyword
        if ($request->type === Config::CONFIG_META_TAG_TYPE) {
            if (!$this->languageService->checkLanguages($request->value['titles']) ||
                !$this->languageService->checkLanguages($request->value['descriptions']) ||
                !$this->languageService->checkLanguages($request->value['keywords'])
            ) {
                return $this->sendValidationFailedJsonResponse();
            }
        }
        // Cast value to integer
        if ($request->type === 'boolean' || $request->type === 'numeric') {
            if ($request->value == 0) {
                $value = '00';
                $castValue = (float)$value;
            } else {
                $castValue = (float)$request->value;
            }
        } else {
            $castValue = $request->value;
        }

        $model = $this->service->create(array_merge($request->all(), [
            'value' => $castValue
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
        //Check type is meta_tag with multi lang for title,des,keyword
        if ($request->type === Config::CONFIG_META_TAG_TYPE && $request->value) {
            if (!empty($request->value['titles']) && !$this->languageService->checkLanguages($request->value['titles']) ||
                !empty($request->value['descriptions']) && !$this->languageService->checkLanguages($request->value['descriptions']) ||
                !empty($request->value['keywords']) && !$this->languageService->checkLanguages($request->value['keywords'])
            ) {
                return $this->sendValidationFailedJsonResponse();
            }
        }

        if (($model->key == 'paypal_method' || $model->key == 'stripe_method') && !$request->get('value')) {
            if (($model->key == 'paypal_method' && !$this->service->findConfigByKey('stripe_method')->value) ||
                ($model->key == 'stripe_method' && !$this->service->findConfigByKey('paypal_method')->value)
            ) {

                return $this->sendValidationFailedJsonResponse(['message' => 'Must have at least one payment method']);
            }
        }
        // Cast value to integer
        if ($request->type === 'boolean' || $request->type === 'numeric') {
            if ($request->value == 0) {
                $value = '00';
                $castValue = (float)$value;
            } else {
                $castValue = (float)$request->value;
            }
        } else {
            $castValue = $request->value;
        }
        //Check value by meta tag type
        $valueMetaTagType = $request->type === Config::CONFIG_META_TAG_TYPE ?
            array_replace_recursive($model->value, $castValue ?? $model->value) :
            $castValue;
        $this->service->update($model, array_merge($request->except(['key']), [
            'value' => $valueMetaTagType
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     */
    public function loadPublicConfig(): JsonResponse
    {
        $models = $this->service->loadPublicConfig();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @return JsonResponse
     */
    public function loadConfigPermission(): JsonResponse
    {
        $models = $this->service->loadConfigPermission();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @return JsonResponse
     */
    public function loadConfigMailbox(): JsonResponse
    {
        $configMailbox = [Config::CONFIG_MAILBOX_MX, Config::CONFIG_MAILBOX_DMARC, Config::CONFIG_MAILBOX_DKIM];
        $models = $this->service->findAllWhereIn('key', $configMailbox);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param UpsertConfigRequest $request
     * @return JsonResponse
     */
    public function upsertConfig(UpsertConfigRequest $request)
    {
        $model = $this->service->findConfigByKey($request->get('key'));

        if (empty($model)) {
            $request = app($this->storeRequest);

            $model = $this->service->create($request->except(['key']));
        } else {

            $this->service->update($model, $request->except(['key']));
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function getCachePlatformConfig($id)
    {
        $cache = $this->service->findOneWhere(['key' => $id . '_cache']);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $cache)
        );
    }

    public function editCachePlatformConfig($platformPackageUuid, UpdateCachePlatformConfig $request)
    {
        $model = $this->service->findOneWhere(['key' => $platformPackageUuid . '_cache']);
        $this->service->update($model, $request->except(['key']));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function testSmtpAccount($id)
    {
        $smtpAccount = $this->service->findOneWhereOrFail([
            'type' => 'smtp_account',
            'uuid' => $id
        ]);

        try {
            $subject = "Test SMTP ACCOUNT";
            $body = "Test SMTP ACCOUNT";

            $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccount->value);

            Mail::to(config('user.email_test'))->send(new SendEmails($subject, $body));

            return $this->sendOkJsonResponse(['message' => __('messages.sent_mail_success')]);
        } catch (\Exception $e) {
            return $this->sendValidationFailedJsonResponse(["smtp_account" => $e->getMessage()]);
        }
    }

    public function indexAdmin(IndexRequest $request)
    {
        $models = $this->service->getAdminConfigsCollectionWithPagination($request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showAdmin($id)
    {
        $model = $this->service->showAdminConfig($id);
        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
