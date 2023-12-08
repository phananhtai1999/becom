<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Company;
use App\Models\QueryBuilders\MyCompanyQueryBuilder;

class MyCompanyService extends AbstractService
{
    protected $modelClass = Company::class;

    protected $modelQueryBuilderClass = MyCompanyQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyCompany($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function showMyAndPublicCompany($id)
    {
        return $this->model->where('uuid', $id)->where(function ($query) {
            $query->where([
                ['user_uuid', auth()->user()],
                ['app_id', auth()->appId()]
            ])
                ->orWhere([
                    ['user_uuid', null],
                    ['app_id', auth()->appId()]
                ]);
        })->firstOrFail();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyCompany($id)
    {
        $company = $this->showMyCompany($id);

        return $this->destroy($company->getKey());
    }
}
