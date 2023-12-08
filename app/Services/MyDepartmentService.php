<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Department;
use App\Models\QueryBuilders\MyDepartmentQueryBuilder;

class MyDepartmentService extends AbstractService
{
    protected $modelClass = Department::class;

    protected $modelQueryBuilderClass = MyDepartmentQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyDepartment($id)
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
    public function showMyAndPublicDepartment($id)
    {
        return $this->model->where('uuid', $id)->where(function ($query) {
            $query->where([
                ['user_uuid', auth()->user()],
                ['app_id', auth()->appId()]
            ])
                ->orWhereNull('user_uuid');
        })->firstOrFail();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyDepartment($id)
    {
        $department = $this->showMyDepartment($id);

        return $this->destroy($department->getKey());
    }
}
