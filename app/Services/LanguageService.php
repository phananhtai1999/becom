<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Language;
use App\Models\QueryBuilders\LanguageQueryBuilder;

class LanguageService extends AbstractService
{
    protected $modelClass = Language::class;

    protected $modelQueryBuilderClass = LanguageQueryBuilder::class;

    /**
     * @return mixed
     */
    public function getAllLanguage()
    {
        return $this->model->all();
    }
}
