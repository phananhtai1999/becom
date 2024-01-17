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
use App\Http\Requests\DashboardPartnerChartRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\PartnerDetailRequest;
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
use App\Models\UserProfile;
use App\Services\PartnerLevelService;
use App\Services\PartnerPayoutService;
use App\Services\PartnerService;
use App\Services\PartnerTrackingByYearService;
use App\Services\PartnerTrackingService;
use App\Services\PartnerUserService;
use App\Services\SmtpAccountService;
use App\Services\UserPaymentByDayService;
use App\Services\UserProfileService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait;

    protected $partnerLevelService;

    protected $userService;

    protected $smtpAccountService;

    protected $partnerUserService;

    protected $partnerTrackingService;

    protected $userPaymentByDayService;

    protected $partnerTrackingByYearService;

    protected $partnerPayoutService;

    public function __construct(
        PartnerService $service,
        PartnerLevelService $partnerLevelService,
        UserService $userService,
        SmtpAccountService $smtpAccountService,
        PartnerUserService $partnerUserService,
        PartnerTrackingService $partnerTrackingService,
        UserPaymentByDayService $userPaymentByDayService,
        PartnerTrackingByYearService $partnerTrackingByYearService,
        PartnerPayoutService $partnerPayoutService,
        UserProfileService $userProfileService
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
        $this->partnerPayoutService = $partnerPayoutService;
        $this->userProfileService = $userProfileService;
    }

    /**
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $sort = $request->get('sort');
        $listSortAttribute = ["clicks", "-clicks", "sign_up", "-sign_up", "customers", "-customers"];
        if (in_array($sort, $listSortAttribute)) {
            $models = $this->service->sortByAttributeOfPartner($request);
        } else {
            $models = $this->service->getCollectionWithPagination();
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
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
            'publish_status' => 'pending',
            'app_id' => auth()->appId()
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
            $minCode = $this->userProfileService->getMinCodeByNumberOfUser();
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
            $partnerUser = $this->partnerUserService->findOneWhere([
                'user_uuid' => $userUuid,
                'app_id' => $model->app_id
            ]);
            if ($partnerUser) {
                $this->partnerUserService->update($partnerUser, [
                    'partner_code' => $code,
                    'partnered_at' => Carbon::now()
                ]);
            }else {
                $this->partnerUserService->create([
                    'user_uuid' => $userUuid,
                    'app_id' => $model->app_id,
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
        $partner = $this->service->findOneWhereOrFail([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);
        $referrals = $this->partnerUserService->referralsOfPartnerInMonth($partner->code)->count();
        $clicks = $this->partnerTrackingService->trackingClicksOfPartnerInMonth($partner->uuid)->count();
        $customers = $this->partnerUserService->numberCustomerPartnerByMonthCurrent($partner->code)->count();

        return $this->sendOkJsonResponse(["data" => [
            "referrals" => $referrals,
            "clicks" => $clicks,
            "customers" => $customers,
            "unpaid_earnings" => $partner->unpaid_earnings
        ]]);
    }

    public function partnerReferrals(PartnerReferralsRequest $request)
    {
        //Email created customer_since
        $partner = $this->service->findOneWhereOrFail([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

        return $this->sendOkJsonResponse(["data" => $this->partnerUserService->referrerStatisticsOfPartnerbyType($partner->code, $request->get('type'))]);

    }

    public function partnerSubAffiliates()
    {
        $partner = $this->service->findOneWhereOrFail([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

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

    public function partnerDetail(PartnerDetailRequest $request)
    {
        $partner = $this->service->findOneWhereOrFail([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

        $groupsData = $labels = $lists = [];
        if (!$request->get('type') || $request->get('type') === 'day'){
            $startDate = Carbon::today()->subDays(6);
            $endDate = Carbon::today();
            $format = "%Y-%m-%d";
            $clicks = $this->partnerTrackingService->trackingClickByDateFormat($format, $startDate, $endDate, $partner->uuid)->toArray();
            $signups = $this->partnerUserService->trackingSignUpByDateFormat($format, $startDate, $endDate, $partner->code)->toArray();
            $customers = $this->userPaymentByDayService->getCustomersPartnerByDate($startDate, $endDate, $partner->code);

            $lists = [$clicks,$signups,$customers];
            //Lấy danh sách các label từ start -> endDate theo type day, week, monthj
            $currentDate = $endDate;
            while ($currentDate >= $startDate) {
                $labels[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->subDay();
            }
        }
        if ($request->get('type') === 'month'){
            $startDate = Carbon::today()->subMonth(5)->startOfMonth();
            $endDate = Carbon::today();
            $format = "%Y-%m";
            $clicks = $this->partnerTrackingService->trackingClickByDateFormat($format, $startDate, $endDate, $partner->uuid)->toArray();
            $signups = $this->partnerUserService->trackingSignUpByDateFormat($format, $startDate, $endDate, $partner->code)->toArray();
            $customers = $this->userPaymentByDayService->getCustomersPartnerByMonth($startDate, $endDate, $partner->code)->toArray();
            $earnings = $this->partnerTrackingByYearService->trackingEarningsOfPartner($startDate, $endDate, $partner->uuid);

            $lists = [$clicks,$signups,$customers, $earnings];
            $currentDate = $endDate;
            while ($currentDate >= $startDate) {
                $labels[] = $currentDate->format('Y-m');
                $currentDate = $currentDate->subMonth();
            }
        }
        if ($request->get('type') === 'week') {
//        $dateString = '2023-14';
//        $year = substr($dateString, 0, 4); // Lấy 4 ký tự đầu tiên của chuỗi là năm
//        $week = substr($dateString, -2); // Lấy 2 ký tự cuối của chuỗi là mã lịch tuần
//        $date = Carbon::now()->setISODate($year, $week)->startOfWeek(0);

            $startDate = Carbon::today()->subWeek(3)->startOfWeek(0);
            $endDate = Carbon::today();
            $format = "%Y-%U";
            $clicks = $this->partnerTrackingService->trackingClickByDateFormat($format, $startDate, $endDate, $partner->uuid)->toArray();
            $signups = $this->partnerUserService->trackingSignUpByDateFormat($format, $startDate, $endDate, $partner->code)->toArray();
            $customers = $this->userPaymentByDayService->trackingCustomersByWeek($startDate, $endDate, $partner->code);

            $lists = [$clicks,$signups,$customers];
            $currentDate = $endDate->startOfWeek(0);
            while ($currentDate >= $startDate) {
                $labels[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->subWeek();
            }
        }

        //Nhóm danh sách clicks, signups, customers,.. có chung label lại thành 1 mảng
        foreach ($lists as $list) {
            foreach ($list as $item) {
                if ($request->get('type') === 'week'){
                    $label = Carbon::today()->setISODate(substr($item['label'], 0, 4), substr($item['label'], -2))
                        ->startOfWeek(0)->format('Y-m-d');
                    $item['label'] = $label;
                }else{
                    $label = $item['label'];
                }
                if (!isset($groupsData[$label])) {
                    $groupsData[$label] = [
                        'label' => $label,
                        'clicks' => 0,
                        'signups' => 0,
                        'customers' => 0,
                        'amount' => 0,
                        'earnings' => 0,
                    ];
                }
                $groupsData[$label] = array_merge($groupsData[$label], $item);
            }
        }

        //Gộp danh sách $groupsData vào trong $labels. Nếu có trong labels thì gán qua còn không có thì cho tất cả dữ liệu là 0
        $results = [];
        foreach ($labels as $label) {
            $found = false;
            foreach ($groupsData as $data) {
                if ($data['label'] === $label) {
                    $results[] = $data;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $results[] = [
                    'label' => $label,
                    'clicks' => 0,
                    'signups' => 0,
                    'customers' => 0,
                    'amount' => 0,
                    'earnings' => 0,
                ];
            }
        }

        return $this->sendOkJsonResponse(["data" => $results]);
    }

    public function partnerRewards()
    {
        $partner = $this->service->findOneWhereOrFail([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);
        $data = $this->userPaymentByDayService->getRewardsCommissionThisMonthByPartner($partner->code);

        return $this->sendOkJsonResponse(["data" => $data]);
    }

    public function UpdateUserPayment()
    {
        Artisan::call('db:seed', [
            '--class' => 'UpdateUserPaymentAndPartnerTrackingSeeder',
        ]);
        return $this->sendOkJsonResponse();
    }

    public function partnersChart(DashboardPartnerChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'date');

        $partnersChart = $this->service->getPartnersChartByGroup($startDate, $endDate, $groupBy);
        $total = $this->service->getTotalPartnersChart($startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $partnersChart,
            'total' => $total
        ]);
    }

    public function signupChart(DashboardPartnerChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'date');
        $partnerUuid = $request->get('partner_uuid');


        if (!$partnerUuid || $partner = $this->service->findOneById($partnerUuid)){
            $partnerCode = $partnerUuid ? $partner->code : null;
            $signupChart = $this->partnerUserService->getSigupChartByGroup($startDate, $endDate, $groupBy, $partnerCode);
            $total =  $this->partnerUserService->getTotalSignUpChart($startDate, $endDate, $partnerCode);

            return $this->sendOkJsonResponse([
                'data' => $signupChart,
                'total' => [
                    'signups' => $total
                ]
            ]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['partner_uuid' => 'The selected partner uuid is invalid.']]);
    }

    public function customersChart(DashboardPartnerChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'date');
        $partnerUuid = $request->get('partner_uuid');


        if (!$partnerUuid || $partner = $this->service->findOneById($partnerUuid)){
            $partnerCode = $partnerUuid ? $partner->code : null;
            $signupChart = $this->userPaymentByDayService->getCustomersChartByGroup($startDate, $endDate, $groupBy, $partnerCode);

            return $this->sendOkJsonResponse([
                'data' => $signupChart,
                'total' => [
                    'customers' => collect($signupChart)->sum('customers'),
                    'amount' => collect($signupChart)->sum('amount'),
                ]
            ]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['partner_uuid' => 'The selected partner uuid is invalid.']]);
    }

    public function earningsChart(DashboardPartnerChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'date');
        $partnerUuid = $request->get('partner_uuid');


        if (!$partnerUuid || $partner = $this->service->findOneById($partnerUuid)){
            $partnerCode = $partnerUuid ? $partner->code : null;
            $signupChart = $this->userPaymentByDayService->getEarningsChartByGroup($startDate, $endDate, $groupBy, $partnerCode);

            return $this->sendOkJsonResponse([
                'data' => $signupChart,
                'total' => [
                    'earnings' => collect($signupChart)->sum('earnings'),
                ]
            ]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['partner_uuid' => 'The selected partner uuid is invalid.']]);
    }
}
