<?php

namespace Database\Seeders;

use App\Models\AddOnSubscriptionHistory;
use App\Models\CreditPackageHistory;
use App\Models\Partner;
use App\Models\PartnerTrackingByYear;
use App\Models\PartnerUser;
use App\Models\SubscriptionHistory;
use App\Models\UserPaymentByDay;
use App\Services\PartnerLevelService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateUserPaymentAndPartnerTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $addOn = AddOnSubscriptionHistory::join('add_on_subscription_plans as a', 'a.uuid', '=' , 'add_on_subscription_histories.add_on_subscription_plan_uuid')
            ->join('add_ons as ao', 'ao.uuid', '=', 'a.add_on_uuid')
            ->selectRaw("add_on_subscription_histories.user_uuid, DATE(add_on_subscription_histories.created_at) as created_at,IF(a.duration_type = 'month', ao.monthly, ao.yearly) as payment")
            ->whereNull('add_on_subscription_histories.deleted_at');
        $platfromPackage = SubscriptionHistory::join('subscription_plans as s', 's.uuid', '=', 'subscription_histories.subscription_plan_uuid')
            ->join('platform_packages as pp', 'pp.uuid', '=', 's.platform_package_uuid')
            ->selectRaw("subscription_histories.user_uuid, DATE(subscription_histories.created_at) as created_at,IF(s.duration_type = 'month', pp.monthly, pp.yearly) as payment")
            ->whereNull('subscription_histories.deleted_at');

        $payments = CreditPackageHistory::join('credit_packages as c', 'c.uuid', '=', 'credit_package_histories.credit_package_uuid')
            ->selectRaw("credit_package_histories.user_uuid, DATE(credit_package_histories.created_at) as created_at, c.price as payment")
            ->whereNull('credit_package_histories.deleted_at')
            ->unionall($addOn)->unionall($platfromPackage)->get();

        $newCollection = $payments
            ->groupBy(function ($item) {
                // Group theo user_uuid, month và year
                return $item->user_uuid . '-' . date('m-Y', strtotime($item->created_at));
            })
            ->map(function ($group) {
                // Tính tổng payment của cả nhóm
                $totalPayment = $group->sum('payment');

                // Lấy thông tin user_uuid, month và year từ phần tử đầu tiên trong nhóm
                $firstItem = $group->first();
                $userUuid = $firstItem->user_uuid;
                $month = date('n', strtotime($firstItem->created_at));
                $year = date('Y', strtotime($firstItem->created_at));
                $paymentByDay = $group
                    ->sortBy('created_at')
                    ->groupBy(function ($item) {
                        // Group theo ngày trong tháng
                        return date('j', strtotime($item->created_at));
                    })
                    ->map(function ($group) {
                        // Tính tổng payment của nhóm
                        return $group->sum('payment');
                    })->toArray();
                // Tạo một array chứa các payment theo ngày trong tháng
                // Trả về một array mới với định dạng mong muốn
                return [
                    'user_uuid' => $userUuid,
                    'payment' => $paymentByDay,
                    'month' => (int)$month,
                    'year' => (int)$year,
                    'total_payment' => $totalPayment,
                    'created_at' => (int)$year . '-' . (int)$month .'-'. array_key_first($paymentByDay),
                    'updated_at' => (int)$year . '-' . (int)$month .'-'. array_key_last($paymentByDay),
                ];
            })->values();
        foreach ($newCollection as $userPayment) {
            UserPaymentByDay::updateOrCreate(
                [
                    'user_uuid' => $userPayment['user_uuid'],
                    'month' => $userPayment['month'],
                    'year' => $userPayment['year'],
                ],
                [
                    'payment' => $userPayment['payment'],
                    'total_payment' => $userPayment['total_payment'],
                    'created_at' => $userPayment['created_at'],
                    'updated_at' => $userPayment['updated_at'],
                ]
            );
        }

        $this->updateOrCreatePartnerTracking();
    }


    public function updateOrCreatePartnerTracking()
    {
        $usersPartner = PartnerUser::whereNotNull('registered_from_partner_code')->whereIn('partner_user.user_uuid', function ($query) {
            $query->select('a.user_uuid')
                ->from('user_payment_by_day as a')
                ->whereColumn('a.user_uuid', 'partner_user.user_uuid');
        })->get()->groupBy('registered_from_partner_code');
        $results = [];
        foreach ($usersPartner as $key => $item) {
            $commissions = [];
            $numberUserByMonthYear = UserPaymentByDay::whereIn('user_uuid', $item->pluck('user_uuid')->toArray())
                ->selectRaw("Count(uuid) as count, month, year")->groupBy('year', 'month')->get();
            foreach ($numberUserByMonthYear as $commission){
                $commissions[$commission->year][$commission->month] = (new PartnerLevelService())->getPartnerLevelByNumberCustomer($commission->count)->commission;
            }
            $userPayments = UserPaymentByDay::whereIn('user_uuid', $item->pluck('user_uuid')->toArray())
                ->get()->groupBy('year')->map(function ($item, $keyYear) use ($commissions){
                    $today = Carbon::today();
                    if ($today->year === $keyYear && !$today->isLastOfMonth()){
                        $item = $item->where('month', '<>', $today->month);
                    }
                    $commission = $item->sortBy('month')->groupBy('month')->map(function ($item, $keyMonth) use ($keyYear, $commissions){
                        return $item->sum('total_payment') * $commissions[$keyYear][$keyMonth] / 100;
                    });
                    return [
                        'year' => $keyYear,
                        'commission' => $commission,
                        'total_commission' => $commission->sum()
                    ];
                });
            foreach ($userPayments as $userPayment) {
                $results[] = [
                    'partner_uuid' => Partner::where('code', $key)->withTrashed()->first()->uuid,
                    'commission' => $userPayment['commission'],
                    'total_commission' => $userPayment['total_commission'],
                    'year' => $userPayment['year']
                ];
            }
        }

        foreach ($results as $partner_tracking){
            PartnerTrackingByYear::updateOrCreate(
                [
                    'partner_uuid' => $partner_tracking['partner_uuid'],
                    'year' => $partner_tracking['year'],
                ],
                [
                    'commission' => $partner_tracking['commission'],
                    'total_commission' => $partner_tracking['total_commission'],
                ]
            );
        }
    }
}
