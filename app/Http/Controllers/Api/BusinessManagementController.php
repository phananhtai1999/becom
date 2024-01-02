<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AddBusinessMemberRequest;
use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Requests\BlockBusinessMemberRequest;
use App\Http\Requests\BusinessManagementRequest;
use App\Http\Requests\GetAddOnOfBusinessRequest;
use App\Http\Requests\GetBusinessMemberRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyBusinessManagementRequest;
use App\Http\Requests\RemoveBusinessMemberRequest;
use App\Http\Requests\UpdateBusinessManagementRequest;
use App\Http\Requests\UpdateMyBusinessManagementRequest;
use App\Http\Resources\AddOnResourceCollection;
use App\Http\Resources\BusinessManagementResource;
use App\Http\Resources\BusinessManagementResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Resources\UserBusinessResource;
use App\Http\Resources\UserBusinessResourceCollection;
use App\Models\PlatformPackage;
use App\Models\Role;
use App\Models\Team;
use App\Models\UserBusiness;
use App\Services\AddOnService;
use App\Services\BusinessManagementService;
use App\Services\CstoreService;
use App\Services\DomainService;
use App\Services\MyBusinessManagementService;
use App\Services\MyDomainService;
use App\Services\SendProjectService;
use App\Services\UserAddOnService;
use App\Services\UserBusinessService;
use App\Services\UserService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Techup\Mailbox\Facades\Mailbox;

class BusinessManagementController extends AbstractRestAPIController
{
    use RestIndexTrait, RestDestroyTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MyBusinessManagementService
     */
    protected $myService;

    /**
     * @var DomainService
     */
    protected $domainService;

    /**
     * @var MyDomainService
     */
    protected $myDomainService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var CstoreService
     */
    protected $cstoreService;

    /**
     * @param BusinessManagementService $service
     * @param MyBusinessManagementService $myService
     * @param DomainService $domainService
     * @param MyDomainService $myDomainService
     */
    public function __construct(
        BusinessManagementService   $service,
        MyBusinessManagementService $myService,
        DomainService               $domainService,
        MyDomainService             $myDomainService,
        UserBusinessService         $userBusinessService,
        UserService                 $userService,
        UserAddOnService            $userAddOnService,
        AddOnService                $addOnService,
        SendProjectService          $sendProjectService,
        CstoreService               $cstoreService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->domainService = $domainService;
        $this->myDomainService = $myDomainService;
        $this->userBusinessService = $userBusinessService;
        $this->sendProjectService = $sendProjectService;
        $this->resourceCollectionClass = BusinessManagementResourceCollection::class;
        $this->resourceClass = BusinessManagementResource::class;
        $this->userBusinessResourceClass = UserBusinessResource::class;
        $this->userBusinessResourceCollectionClass = UserBusinessResourceCollection::class;
        $this->addOnResourceCollectionClass = AddOnResourceCollection::class;
        $this->storeRequest = BusinessManagementRequest::class;
        $this->editRequest = UpdateBusinessManagementRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->userService = $userService;
        $this->userAddOnService = $userAddOnService;
        $this->addOnService = $addOnService;
        $this->cstoreService = $cstoreService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        // User only have one Business Management
        $businessManagement = $this->service->checkBusinessManagementOfUser($request->get('owner_uuid') ?? auth()->user()->getKey());
        if ($businessManagement) {
            return $this->sendValidationFailedJsonResponse();
        }
        $model = $this->service->create(array_merge($request->except('domain_uuid'), [
            'owner_uuid' => $request->get('owner_uuid') ?? auth()->user()->getKey(),
        ]));

        //check Domain Business Of User Exists Or Not
        $domain = $this->domainService->updateOrCreateDomainByBusiness($request->domain, $model);
        //Set Domain Default for Business
        $this->service->setDomainDefault($model, $domain->uuid);
        $model->businessCategories()->attach($request->get('business_categories', []));
        $this->sendProjectService->create([
            'domain' => $request->get('domain'),
            'business_uuid' => $model->uuid,
            'user_uuid' =>  $request->get('owner_uuid') ?? auth()->user()->getKey(),
            'name' => $request->get('name'),
            'logo' => $request->get('avatar'),
            'description' => $request->get('introduce'),
            'domain_uuid' => $domain->uuid,
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);
        $model = $this->service->findOrFailById($id);

        //User only have one Business Management
        $businessManagement = $this->service->checkBusinessManagementOfUser($request->get('owner_uuid'));
        if ($businessManagement && $model->owner_uuid != $request->get('owner_uuid')) {

            return $this->sendValidationFailedJsonResponse();
        }
        $this->service->update($model, $request->except('domain_uuid'));
        $model->businessCategories()->sync($request->business_categories ?? $model->business_categories);

        if ($request->domain) {
            //check Domain Business Of User Exists Or Not
            $domain = $this->domainService->updateOrCreateDomainByBusiness($request->domain, $model);
            //Set Domain Default for Business
            if ($domain->verified_at) {
                $this->service->setDomainDefault($model, $domain->uuid);
            }
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param MyBusinessManagementRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyBusinessManagement(MyBusinessManagementRequest $request)
    {
        // User only have one Business Management
        $businessManagement = $this->service->checkBusinessManagementOfUser(auth()->user()->getKey());
        if ($businessManagement) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create(array_merge($request->except('domain_uuid'), [
            'owner_uuid' => auth()->user()->getkey(),
        ]));

        //check Domain Business Of User Exists Or Not
        $domain = $this->myDomainService->updateOrCreateDomainByBusiness($request->domain, $model);
        //Set Domain Default for Business
        $this->service->setDomainDefault($model, $domain->uuid);

        $model->businessCategories()->attach($request->get('business_categories', []));
        $this->sendProjectService->create([
            'domain' => $request->get('domain'),
            'business_uuid' => $model->uuid,
            'user_uuid' => auth()->user()->getKey(),
            'name' => $request->get('name'),
            'logo' => $request->get('avatar'),
            'description' => $request->get('introduce'),
            'domain_uuid' => $domain->uuid,
        ]);

        if($this->cstoreService->storeS3Config($request)){
            $this->cstoreService->storeFolderByType($request->get('name'), $model->uuid, config('foldertypecstore.BUSINESS'));
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyBusinessManagement($id)
    {
        $model = $this->myService->showMyBusinessManagement($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyBusinessManagementRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyBusinessManagement(UpdateMyBusinessManagementRequest $request, $id)
    {
        $model = $this->myService->showMyBusinessManagement($id);

        $this->service->update($model, array_merge($request->except('domain_uuid'), [
            'owner_uuid' => auth()->user()->getkey(),
        ]));
        $model->businessCategories()->sync($request->business_categories ?? $model->business_categories);

        if ($request->domain) {
            //check Domain Business Of User Exists Or Not
            $domain = $this->myDomainService->updateOrCreateDomainByBusiness($request->domain, $model);
            //Set Domain Default for Business
            if ($domain->verified_at) {
                $this->service->setDomainDefault($model, $domain->uuid);
            }
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyBusinessManagement($id)
    {
        $this->myService->deleteMyBusinessManagement($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param AddBusinessMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBusinessMember(AddBusinessMemberRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
                $businessUuid = $request->get("business_uuid");
            } else {
                $businesses = $this->user()->businessManagements;
                if ($businesses->toArray()) {
                    $businessUuid = $businesses->first()->uuid;
                } else {

                    return $this->sendJsonResponse(false, 'Does not have business', [], 403);
                }
            }
            if ($request->get('type') == UserBusiness::ALREADY_EXISTS_ACCOUNT) {
                foreach ($request->get('user_uuids') as $userUuid) {
                    $existingRecord = $this->userBusinessService->findOneWhere([
                        'business_uuid' => $businessUuid,
                        'user_uuid' => $userUuid
                    ]);

                    if (!$existingRecord) {
                        $this->userBusinessService->create([
                            'business_uuid' => $businessUuid,
                            'user_uuid' => $userUuid
                        ]);
                    }
                }
                DB::commit();
                return $this->sendCreatedJsonResponse();
            } elseif ($request->get('type') == UserBusiness::ACCOUNT_INVITE) {
                $passwordRandom = $this->generateRandomString(10);
                $email = $request->get('username') . '@' . $request->get('domain');
                $user = $this->userService->create([
                    'email' => $email,
                    'first_name' => $request->get('first_name'),
                    'last_name' => $request->get('last_name'),
                    'username' => $request->get('username'),
                    'can_add_smtp_account' => 0,
                    'password' => Hash::make($request->get('password'))
                ]);
                $user->roles()->attach([config('user.default_role_uuid')]);
                $user->userPlatformPackage()->create(['platform_package_uuid' => PlatformPackage::DEFAULT_PLATFORM_PACKAGE_1]);
                $this->userBusinessService->create([
                    'business_uuid' => $businessUuid,
                    'user_uuid' => $user->uuid
                ]);
//                Mailbox::postEmailAccountcreate($user->uuid, $email, $passwordRandom);
                DB::commit();

                return $this->sendCreatedJsonResponse();
            }

        } catch (ConnectionException $exception) {
            DB::rollBack();
            return $this->sendInternalServerErrorJsonResponse();
        }

    }

    public function getAddOns(GetAddOnOfBusinessRequest $request)
    {
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
            $businessUuid = $request->get("business_uuid");
        } else {
            $businesses = $this->user()->businessManagements;
            if ($businesses->toArray()) {
                $businessUuid = $businesses->first()->uuid;
            } else {

                return $this->sendJsonResponse(false, 'Does not have business', [], 403);
            }
        }
        $addOns = $this->addOnService->getAddOnsByBusiness($request, $businessUuid, $request->get('exclude_team_uuid'));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->addOnResourceCollectionClass, $addOns)
        );
    }

    public function listMemberOfBusiness(GetBusinessMemberRequest $request)
    {
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
            $businessUuid = $request->get("business_uuid");
        } else {
            $businesses = $this->user()->businessManagements;
            if ($businesses->toArray()) {
                $businessUuid = $businesses->first()->uuid;
            } else {

                return $this->sendJsonResponse(false, 'Does not have business', [], 403);
            }
        }

        $model = $this->userBusinessService->listBusinessMember([$businessUuid], $request, $request->get('exclude_team_uuid'));
        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userBusinessResourceCollectionClass, $model)
        );
    }

    public function blockBusinessMember($id, BlockBusinessMemberRequest $request)
    {
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->first()) {
            $businessUuid = $request->get("business_uuid");
        } else {
            $businesses = $this->user()->businessManagements;
            if ($businesses->toArray()) {
                $businessUuid = $businesses->first()->uuid;
            } else {

                return $this->sendJsonResponse(false, 'Does not have business', [], 403);
            }
        }
        $userBusiness = $this->userBusinessService->findOneWhereOrFail(['business_uuid' => $businessUuid, 'user_uuid' => $id]);
        $userBusiness->update(['is_blocked' => !$userBusiness->is_blocked]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userBusinessResourceClass, $userBusiness)
        );
    }

    public function removeBusinessMember($id, RemoveBusinessMemberRequest $request)
    {
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->first()) {
            $businessUuid = $request->get("business_uuid");
        } else {
            $businesses = $this->user()->businessManagements;
            if ($businesses->toArray()) {
                $businessUuid = $businesses->first()->uuid;
            } else {

                return $this->sendJsonResponse(false, 'You do not have business', [], 403);
            }
        }
        $userBusiness = $this->userBusinessService->findOneWhereOrFail(['business_uuid' => $businessUuid, 'user_uuid' => $id]);
        $userBusiness->delete();

        return $this->sendCreatedJsonResponse();
    }
}
