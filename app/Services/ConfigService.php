<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Config;
use App\Models\QueryBuilders\ConfigQueryBuilder;
use App\Models\Role;

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
        if (auth()->hasRole([Role::ROLE_ROOT])) {
            return $this->loadAllConfig();
        } else {
            return $this->model->whereIn('status', [Config::CONFIG_PRIVATE_STATUS, Config::CONFIG_PUBLIC_STATUS])
                ->orwhereIn('key', [Config::CONFIG_EMAIL_PRICE, Config::CONFIG_SMS_PRICE, Config::CONFIG_TELEGRAM_PRICE, Config::CONFIG_VIBER_PRICE])->get();
        }
    }

    public function getAdminConfigsCollectionWithPagination($request)
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereIn('status', [Config::CONFIG_PRIVATE_STATUS, Config::CONFIG_PUBLIC_STATUS])
            ->orwhereIn('key', [Config::CONFIG_EMAIL_PRICE, Config::CONFIG_SMS_PRICE, Config::CONFIG_TELEGRAM_PRICE, Config::CONFIG_VIBER_PRICE])
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function showAdminConfig($id)
    {
        return $this->model->where(function ($q) {
            $q->whereIn('status', [Config::CONFIG_PRIVATE_STATUS, Config::CONFIG_PUBLIC_STATUS])
                ->orwhereIn('key', [Config::CONFIG_EMAIL_PRICE, Config::CONFIG_SMS_PRICE, Config::CONFIG_TELEGRAM_PRICE, Config::CONFIG_VIBER_PRICE]);
        })->where('uuid', $id)->firstOrFail();
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

    public function multiLangForValueWithMetaTagType($type, $value)
    {
        //Check auth:api
        $check = false;
        if (auth()->guest() || !optional(optional(optional(auth()->user())->roles)->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN]))->count()) {
            $check = true;
        }

        if ($type === Config::CONFIG_META_TAG_TYPE) {
            $currentLanguage = request()->cookie('lang') ?? app()->getLocale();
            $structuredFields = ['titles', 'descriptions', 'keywords', 'copyrights', 'authors', 'resource-types', 'distributions', 'revisit-afters', 'GENERATORS'];
            foreach ($structuredFields as $fields) {
                $field = substr($fields, 0, -1);
                $value[$field] = !empty($value[$fields][$currentLanguage]) ? $value[$fields][$currentLanguage] : $value[$fields][config('app.fallback_locale')];
                $value[$fields] = $check ? $value[$field] : $value[$fields];
            }
        }

        return $value;
    }
}
