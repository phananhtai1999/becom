<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Language;
use App\Models\QueryBuilders\LanguageQueryBuilder;
use Illuminate\Support\Facades\File;

class LanguageService extends AbstractService
{
    protected $modelClass = Language::class;

    protected $modelQueryBuilderClass = LanguageQueryBuilder::class;

    /**
     * @return mixed
     */
    public function getAllCodeLanguage()
    {
        return $this->model->pluck('code');
    }

    /**
     * @param $dataLanguages
     * @return bool
     */
    public function checkLanguages($dataLanguages)
    {
        foreach ($dataLanguages as $lang => $value) {
            if (!$this->getAllCodeLanguage()->contains($lang)) {
                return false;
            }
        }

        return true;
    }

    public function languagesSupport()
    {
        return array_map('basename', File::directories(resource_path('lang')));
    }
}
