<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AddBusinessMemberRequest;
use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Requests\BusinessManagementRequest;
use App\Http\Requests\GetAddOnOfBusinessRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyBusinessManagementRequest;
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
use App\Services\BusinessManagementService;
use App\Services\DomainService;
use App\Services\MyBusinessManagementService;
use App\Services\MyDomainService;
use App\Services\UserAddOnService;
use App\Services\UserBusinessService;
use App\Services\UserService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        UserBusinessService $userBusinessService,
        UserService $userService,
        UserAddOnService $userAddOnService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->domainService = $domainService;
        $this->myDomainService = $myDomainService;
        $this->userBusinessService = $userBusinessService;
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
        try{
            if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
                $businessUuid = $request->get("business_uuid");
            } else {
                $businessUuid = $this->user()->businessManagements->first()->uuid;
            }
            if($request->get('type') == UserBusiness::ALREADY_EXISTS_ACCOUNT){
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
            }elseif($request->get('type') == UserBusiness::ACCOUNT_INVITE){
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
                Mailbox::postEmailAccountcreate($user->uuid, $email, $passwordRandom);
                DB::commit();

                return $this->sendCreatedJsonResponse();
            }

        } catch (ConnectionException $exception){
            DB::rollBack();
            return $this->sendInternalServerErrorJsonResponse();
        }

    }

    public function getAddOns(GetAddOnOfBusinessRequest $request)
    {
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
            $businessUuid = $request->get("business_uuid");
        } else {
            $businesses= $this->user()->businessManagements;
            if (!empty($businesses)) {
                $businessUuid = $businesses->first()->uuid;
            }
        }
        $business = $this->service->findOrFailById($businessUuid);
        $userAddOns = $this->userAddOnService->findAllWhere(['user_uuid' => $business->owner_uuid], ['user_uuid', 'add_on_subscription_plan_uuid'], true);
        $addOns = [];
        foreach ($userAddOns as $userAddOn) {
            $addOns[] = $userAddOn->addOnSubscriptionPlan->addOn ?? [];
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->addOnResourceCollectionClass, $addOns)
        );
    }

    public function listMemberOfBusiness(IndexRequest $request)
    {
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ADMIN, Role::ROLE_ROOT])->first()) {
            $model = $this->userBusinessService->listMemberOfAllBusiness($request);
        } else {
            $business = $this->service->findOneWhere((['owner_uuid' => $this->user()->getKey()]));
            if ($business) {
                $model = $this->userBusinessService->listBusinessMember([$business->uuid], $request);
            } else {
                $model = [];
            }
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userBusinessResourceCollectionClass, $model)
        );
    }
}
