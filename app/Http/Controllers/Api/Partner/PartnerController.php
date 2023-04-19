<?php

namespace App\Http\Controllers\Api\Partner;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SendAccountForNewPartnerEvent;
use App\Events\SendEmailRecoveryPasswordEvent;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\ChangeStatusPartnerRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerReferralsRequest;
use App\Http\Requests\PartnerRequest;
use App\Http\Requests\PartnerTop10Request;
use App\Http\Requests\RegisterPartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\PartnerResourceCollection;
use App\Mail\SendAccountForNewPartner;
use App\Models\AddOnSubscriptionHistory;
use App\Models\CreditPackageHistory;
use App\Models\Partner;
use App\Models\PartnerLevel;
use App\Models\PartnerTrackingByYear;
use App\Models\PartnerUser;
use App\Models\SubscriptionHistory;
use App\Models\User;
use App\Models\UserPaymentByDay;
use App\Services\PartnerLevelService;
use App\Services\PartnerService;
use App\Services\PartnerTrackingByYearService;
use App\Services\PartnerTrackingService;
use App\Services\PartnerUserService;
use App\Services\SmtpAccountService;
use App\Services\UserPaymentByDayService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    protected $partnerLevelService;

    protected $userService;

    protected $smtpAccountService;

    protected $partnerUserService;

    protected $partnerTrackingService;

    protected $userPaymentByDayService;

    protected $partnerTrackingByYearService;


    public function __construct(
        PartnerService $service,
        PartnerLevelService $partnerLevelService,
        UserService $userService,
        SmtpAccountService $smtpAccountService,
        PartnerUserService $partnerUserService,
        PartnerTrackingService $partnerTrackingService,
        UserPaymentByDayService $userPaymentByDayService,
        PartnerTrackingByYearService $partnerTrackingByYearService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = PartnerResourceCollection::class;
        $this->resourceClass = PartnerResource::class;
        $this->storeRequest = PartnerRequest::class;
        $this->editRequest = UpdatePartnerRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->partnerLevelService = $partnerLevelService;
        $this->userService = $userService;
        $this->smtpAccountService = $smtpAccountService;
        $this->partnerUserService = $partnerUserService;
        $this->partnerTrackingService = $partnerTrackingService;
        $this->userPaymentByDayService = $userPaymentByDayService;
        $this->partnerTrackingByYearService = $partnerTrackingByYearService;
    }

    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->except('code'), [
            'publish_status' => 'active',
        ]));

        return $this->sendOkJsonResponse($this->service->resourceToData($this->resourceClass, $model));
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->except(['code', 'publish_status']));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function registerPartner(RegisterPartnerRequest $request)
    {
        $model = $this->service->create(array_merge($request->except('code'), [
            'publish_status' => 'pending'
        ]));

        return $this->sendOkJsonResponse($this->service->resourceToData($this->resourceClass, $model));
    }

    public function changeStatusPartner(ChangeStatusPartnerRequest $request)
    {
        $model = $this->service->findOrFailById($request->get('partner_uuid'));
        $code = $model->code;
        $userUuid = $model->user_uuid;
        if (!$model->code && $request->get('publish_status') === 'active') {
            //Random code partner
            $minCode = $this->userService->getMinCodeByNumberOfUser();
            do {
                $code = $this->generateRandomString($minCode);
                $modelCode = $this->service->findOneWhere(['code' => $code]);
            } while ($modelCode !== null);

            //Tạo User khi partner chưa có tài khoản hệ thống
            if (!$model->user_uuid) {
                $password = $this->generateRandomString(6);
                $newUser = $this->userService->create([
                    'email' => $model->partner_email,
                    'username' => $model->partner_email,
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'can_add_smtp_account' => 0,
                    'password' => Hash::make($password)
                ]);
                $newUser->roles()->attach($request->get('partner_role'));
                $userUuid = $newUser->uuid;
                Event::dispatch(new SendAccountForNewPartnerEvent($newUser));
            }
            //Kiểm tra partner_user nếu có thì update, k có thì create
            $partnerUser = $this->partnerUserService->findOneWhere(['user_uuid' => $userUuid]);
            if ($partnerUser) {
                $this->partnerUserService->update($partnerUser, [
                    'partner_code' => $code,
                    'partnered_at' => Carbon::now()
                ]);
            }else {
                $this->partnerUserService->create([
                    'user_uuid' => $userUuid,
                    'partner_code' => $code,
                    'partnered_at' => Carbon::now()
                ]);
            }
        }

        $this->service->update($model, [
            'publish_status' => $request->get('publish_status'),
            'code' => $code,
            'user_uuid' => $userUuid
        ]);

        return $this->sendOkJsonResponse();
    }

    public function partnerDashboard()
    {
        $partner = $this->service->findOneWhereOrFail(['user_uuid' => auth()->user()->getKey()]);
        $referrals = $this->partnerUserService->referralsOfPartnerInMonth($partner->code)->count();
        $clicks = $this->partnerTrackingService->trackingClicksOfPartnerInMonth($partner->uuid)->count();
        $customers = $this->partnerUserService->numberCustomerPartnerInMonth($partner->code)->count();
        $unpaid_earnings = $this->partnerTrackingByYearService->earningsOfPartnerByMonth($partner->uuid);

        return $this->sendOkJsonResponse(["data" => [
            "referrals" => $referrals,
            "clicks" => $clicks,
            "customers" => $customers,
            "unpaid_earnings" => $unpaid_earnings
        ]]);
    }

    public function partnerReferrals(PartnerReferralsRequest $request)
    {
        //Email created customer_since
        $partner = $this->service->findOneWhereOrFail(['user_uuid' => auth()->user()->getKey()]);

        return $this->sendOkJsonResponse(["data" => $this->partnerUserService->referrerStatisticsOfPartnerbyType($partner->code, $request->get('type'))]);

    }

    public function partnerSubAffiliates()
    {
        $partner = $this->service->findOneWhereOrFail(['user_uuid' => auth()->user()->getKey()]);

        return $this->sendOkJsonResponse(["data" => $this->partnerUserService->subAffiliatesStatisticsOfPartner($partner->code)]);

    }

    public function partnerTop10(PartnerTop10Request $request)
    {
        $result = [];
        if (!$request->get('type') || $request->get('type') === 'click') {
            $result = $this->partnerTrackingService->getTop10PartnerClick();
        }elseif ($request->get('type') === 'signup') {
            $result = $this->partnerUserService->getTop10PartnerSignUp();
        }elseif ($request->get('type') === 'customer'){
            $result = $this->partnerUserService->getTop10PartnerCustomer();
            dd($result);
        }

         $newResult = $result->map(function ($item) {
           return [
               'name' => $item['full_name'],
               'email' => substr($item['partner_email'], 0, 5) . str_repeat('*', strlen($item['partner_email']) - 10) . substr($item['partner_email'], -5),
               'total' => $item['count']
           ];
        });

        return $this->sendOkJsonResponse(["data" => $newResult]);
    }

    public function partnerDetail()
    {
//        $partner = $this->service->findOneWhereOrFail(['user_uuid' => auth()->user()->getKey()]);
//        $startDate = Carbon::today()->subDays(6);
//        $endDate = Carbon::today();
//        $format = "%Y-%m-%d";
//        $clicks = $this->partnerTrackingService->trackingClickByDateFormat($format, $startDate, $endDate, $partner->uuid);
//        $signups = $this->partnerUserService->trackingSignUpByDateFormat($format, $startDate, $endDate, $partner->code);
//        $customers = $this->partnerUserService->trackingCustomersByDateFormat($format, $startDate, $endDate, $partner->uuid);
//        $earnings;

        $fakeData = [
            [
                'label' => '2023-04-19',
                'clicks' => 10,
                'signups' => 10,
                'customers' => 10,
                'earnings' => 10,
            ],
            [
                'label' => '2023-04-18',
                'clicks' => 10,
                'signups' => 10,
                'customers' => 10,
                'earnings' => 10,
            ],
            [
                'label' => '2023-04-17',
                'clicks' => 10,
                'signups' => 10,
                'customers' => 10,
                'earnings' => 10,
            ],
            [
                'label' => '2023-04-14',
                'clicks' => 10,
                'signups' => 10,
                'customers' => 10,
                'earnings' => 10,
            ],
        ];
        return $this->sendOkJsonResponse(["data" => $fakeData]);
    }

    public function partnerRewards()
    {
        //Tao dữ liệu giả
        $fakeData = [
            [
                'status' => 'unpaid',
                'amount' => 20,
                'from_customer' => 'Nam',
                'created' => '2023-04-19',
            ],
            [
                'status' => 'unpaid',
                'amount' => 120,
                'from_customer' => 'Nam',
                'created' => '2023-04-19',
            ],
            [
                'status' => 'unpaid',
                'amount' => 130,
                'from_customer' => 'Nam',
                'created' => '2023-04-19',
            ],
            [
                'status' => 'unpaid',
                'amount' => 150,
                'from_customer' => 'Nam',
                'created' => '2023-04-19',
            ],
            [
                'status' => 'unpaid',
                'amount' => 200,
                'from_customer' => 'Nam',
                'created' => '2023-04-19',
            ],
        ];
        return $this->sendOkJsonResponse(["data" => $fakeData]);
    }

    public function partnerPayoutTerms()
    {
        $fakeData = [
            [
                'status' => 'success',
                'amount' => 200,
                'created' => '2023-04-19',
                'paid_at' => '2023-04-19'
            ],
            [
                'status' => 'success',
                'amount' => 100,
                'created' => '2023-04-19',
                'paid_at' => '2023-04-19'
            ],
            [
                'status' => 'success',
                'amount' => 50,
                'created' => '2023-04-19',
                'paid_at' => '2023-04-19'
            ],
            [
                'status' => 'success',
                'amount' => 250,
                'created' => '2023-04-19',
                'paid_at' => '2023-04-19'
            ]
        ];
        return $this->sendOkJsonResponse(["data" => $fakeData]);
    }
}
