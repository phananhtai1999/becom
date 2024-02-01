<?php

namespace App\Observers;

use App\Models\BusinessManagement;
use App\Models\Department;
use App\Services\DepartmentService;

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
        $departmentService = new DepartmentService();
        foreach (Department::DEFAULT_NAME as $value) {
            $departmentService->create([
                'business_uuid' => $businessManagement->uuid,
                'is_default' => true,
                'app_id' => auth()->appId(),
                'name' => $value
            ]);
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
