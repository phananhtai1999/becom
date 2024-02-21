<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\App;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\AppQueryBuilder;
use App\Models\Role;
use App\Models\UserApp;
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
        $user = UserProfile::where(['user_uuid' => $userId])->firstOrFail();
        $teams = $user->user_teams;
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
        })->pluck('uuid');
    }

    public function myOwnerApps($request, $userId)
    {
        $user = UserProfile::where(['user_uuid' => $userId])->firstOrFail();
        $indexRequest = $this->getIndexRequest($request);

        return AppQueryBuilder::initialQuery()->where(function ($query) use ($user) {
            $query->whereHas('userApps', function ($query) use ($user) {
                $query->where('user_app.user_uuid', $user->user_uuid)
                ->where('app_id', auth()->appId());
            });
        }) ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);

    }

    public function getAppByDepartment($request, $id)
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where(function ($query) use ($id) {
                $query->whereHas('departments', function ($query) use ($id) {
                    $query->where('departments.uuid', $id);
                });
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
