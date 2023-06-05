<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Config;
use App\Models\QueryBuilders\ConfigQueryBuilder;

class ConfigService extends AbstractService
{
    protected $modelClass = Config::class;

    protected $modelQueryBuilderClass = ConfigQueryBuilder::class;

    /**
     * @return mixed
     */
    public function loadAllConfig()
    {
        return $this->model->all();
    }

    /**
     * @return mixed
     */
    public function loadPublicConfig()
    {
        return $this->model->where('status', Config::CONFIG_PUBLIC_STATUS)->get();
    }

    /**
     * @return mixed
     */
    public function loadConfigPermission()
    {
        //Check guest
        if (auth()->guest()) {
            return $this->loadPublicConfig();
        }
        //Check auth:api
        if (auth()->user()->roles->whereIn('slug', ["root"])->count()) {
            return $this->loadAllConfig();
        } else {
            return $this->model->whereIn('status', [Config::CONFIG_PRIVATE_STATUS, Config::CONFIG_PUBLIC_STATUS])
                ->orwhereIn('key', [Config::CONFIG_EMAIL_PRICE, Config::CONFIG_SMS_PRICE, Config::CONFIG_TELEGRAM_PRICE, Config::CONFIG_VIBER_PRICE])->get();
        }
    }

    /**
     * @param $configKey
     * @return mixed
     */
    public function findConfigByKey($configKey)
    {
        return $this->model
            ->where('key', $configKey)
            ->first();
    }

    public function getListPriceByType()
    {
        return $this->model->select('key', 'value')->whereIn('key', [Config::CONFIG_EMAIL_PRICE, Config::CONFIG_SMS_PRICE, Config::CONFIG_TELEGRAM_PRICE, Config::CONFIG_VIBER_PRICE])
            ->get()->keyBy(function ($item) {
                return str_replace("_price", "", $item->key);
            })->toArray();
    }
}
