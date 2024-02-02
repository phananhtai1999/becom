<?php

namespace App\Observers;

use App\Models\BusinessManagement;
use App\Models\Department;
use App\Services\DepartmentService;
use Techup\ApiConfig\Services\ConfigService;

class BusinessObserver
{
    /**
     * Handle the BusinessManagement "created" event.
     *
     * @param  \App\Models\BusinessManagement  $businessManagement
     * @return void
     */
    public function created(BusinessManagement $businessManagement)
    {
        $configService = new ConfigService();
        $defaultDepartment = $configService->findConfigByKey('default_department');
        if ($defaultDepartment) {
            $departmentService = new DepartmentService();
            foreach (json_decode($defaultDepartment->default_value) as $value) {
                $departmentService->create([
                    'business_uuid' => $businessManagement->uuid,
                    'is_default' => true,
                    'app_id' => auth()->appId(),
                    'name' => $value
                ]);
            }
        }

    }

    /**
     * Handle the BusinessManagement "updated" event.
     *
     * @param  \App\Models\BusinessManagement  $businessManagement
     * @return void
     */
    public function updated(BusinessManagement $businessManagement)
    {
        //
    }

    /**
     * Handle the BusinessManagement "deleted" event.
     *
     * @param  \App\Models\BusinessManagement  $businessManagement
     * @return void
     */
    public function deleted(BusinessManagement $businessManagement)
    {
        //
    }

    /**
     * Handle the BusinessManagement "restored" event.
     *
     * @param  \App\Models\BusinessManagement  $businessManagement
     * @return void
     */
    public function restored(BusinessManagement $businessManagement)
    {
        //
    }

    /**
     * Handle the BusinessManagement "force deleted" event.
     *
     * @param  \App\Models\BusinessManagement  $businessManagement
     * @return void
     */
    public function forceDeleted(BusinessManagement $businessManagement)
    {
        //
    }
}
