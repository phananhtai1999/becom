<?php

namespace App\Services;

use Techup\ApiConfig\Models\Config;


class ConfigService extends \Techup\ApiConfig\Services\ConfigService
{
   public function getListPriceByType()
    {
        return $this->model->select('key', 'value')->whereIn('key', [Config::CONFIG_EMAIL_PRICE, Config::CONFIG_SMS_PRICE, Config::CONFIG_TELEGRAM_PRICE, Config::CONFIG_VIBER_PRICE])
            ->get()->keyBy(function ($item) {
                return str_replace("_price", "", $item->key);
            })->toArray();
    }
}
