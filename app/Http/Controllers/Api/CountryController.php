<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CountryResourceCollection;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\CountryService;

class CountryController extends AbstractRestAPIController
{
    use RestStoreTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    public function __construct(
        CountryService   $service,
        ConfigService $configService
    )
    {
        $this->service = $service;
        $this->configService = $configService;
        $this->resourceCollectionClass = CountryResourceCollection::class;
        $this->resourceClass = CountryResource::class;
        $this->storeRequest = CountryRequest::class;
        $this->editRequest = UpdateCountryRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    public function index()
    {
        $smsPrice = $this->configService->findOneWhere(['key' => 'sms_price']);
        $viberPrice = $this->configService->findOneWhere(['key' => 'viber_price']);
        $telegramPrice = $this->configService->findOneWhere(['key' => 'telegram_price']);
        $emailPrice = $this->configService->findOneWhere(['key' => 'email_price']);
        $countries = $this->service->getCollectionWithPagination();
        foreach ($countries as $country) {
            if(empty($country->sms_price)) {
                $country->sms_price = $smsPrice->value;
            }
            if(empty($country->viber_price)) {
                $country->viber_price = $viberPrice->value;
            }
            if(empty($country->telegram_price)) {
                $country->telegram_price = $telegramPrice->value;
            }
            if(empty($country->email_price)) {
                $country->email_price = $emailPrice->value;
            }
        }
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $countries
            ));
    }
}
