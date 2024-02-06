<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\App;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\AppQueryBuilder;
use App\Models\UserProfile;

class AppService extends AbstractService
{
    protected $modelClass = App::class;
    protected $modelQueryBuilderClass = AppQueryBuilder::class;

    public function checkIncludePlatform($platformPackageUuid)
    {
        if (auth()->user()->platform_package == 'professional' && in_array($platformPackageUuid, App::PROFESSIONAL_INCLUDE)) {

            return true;
        } elseif (auth()->user()->platform_package == 'business' && in_array($platformPackageUuid, App::BUSINESS_INCLUDE)) {

            return true;
        }

        return false;
    }


    public function myApps($userId)
    {
        $user = UserProfile::where(['user_uuid' => $userId, 'app_id' => auth()->appId()])->firstOrFail();
        $teams = $user->teams;
        $departments = [];
        if ($teams) {
            foreach ($teams as $team) {
                if ($team->department) {
                    $departments[] = $team->department->uuid;
                }
            }
        }
        return AppQueryBuilder::initialQuery()->where(function ($query) use ($departments) {
            $query->whereHas('departments', function ($query) use ($departments) {
                $query->whereIn('departments.uuid', $departments);
            });
        })->get();
    }
}
