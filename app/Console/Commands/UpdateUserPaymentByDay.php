<?php

namespace App\Console\Commands;

use App\Models\AddOnSubscriptionHistory;
use App\Models\CreditPackageHistory;
use App\Models\SubscriptionHistory;
use App\Models\UserPaymentByDay;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UpdateUserPaymentByDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:user-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::today();
        $todayFormat = $today->format('Y-m-d');
        $subDay = $today->copy()->subDay();
        $subDayFormat = $subDay->format('Y-m-d');
        if (!Redis::get($subDayFormat)) {
            $this->updateUserPaymentByDay($subDay);
            Redis::set($subDayFormat, true);
        }
        Redis::set($todayFormat, false);
        $this->updateUserPaymentByDay($today);
        Redis::set($todayFormat, true);
    }

    public function updateUserPaymentByDay($time)
    {
        $addOn = AddOnSubscriptionHistory::join('add_on_subscription_plans as a', 'a.uuid', '=' , 'add_on_subscription_histories.add_on_subscription_plan_uuid')
            ->join('add_ons as ao', 'ao.uuid', '=', 'a.add_on_uuid')
            ->selectRaw("add_on_subscription_histories.user_uuid, DATE(add_on_subscription_histories.created_at) as created_at,IF(a.duration_type = 'month', ao.monthly, ao.yearly) as payment")
            ->whereDate('add_on_subscription_histories.created_at', $time)
            ->whereNull('add_on_subscription_histories.deleted_at');
        $platfromPackage = SubscriptionHistory::join('subscription_plans as s', 's.uuid', '=', 'subscription_histories.subscription_plan_uuid')
            ->join('platform_packages as pp', 'pp.uuid', '=', 's.platform_package_uuid')
            ->selectRaw("subscription_histories.user_uuid, DATE(subscription_histories.created_at) as created_at,IF(s.duration_type = 'month', pp.monthly, pp.yearly) as payment")
            ->whereDate('subscription_histories.created_at', $time)
            ->whereNull('subscription_histories.deleted_at');

        $payments = CreditPackageHistory::join('credit_packages as c', 'c.uuid', '=', 'credit_package_histories.credit_package_uuid')
            ->selectRaw("credit_package_histories.user_uuid, DATE(credit_package_histories.created_at) as created_at, c.price as payment")
            ->whereDate('credit_package_histories.created_at', $time)
            ->whereNull('credit_package_histories.deleted_at')
            ->unionall($addOn)->unionall($platfromPackage)->get()->groupBy('user_uuid')->map(function ($item, $key) {
                return $item->sum('payment');
            });

        foreach ($payments as $userUuid => $paymentInDay) {
            $userPayment = UserPaymentByDay::where([
                ['user_uuid', $userUuid],
                ['month' , $time->month],
                ['year' , $time->year],
            ])->first();
            if ($userPayment) {
                $payment = $userPayment->payment;
                if (!array_key_exists($time->day, $payment)){
                    $payment[$time->day] = $paymentInDay;
                    $userPayment->update([
                        'payment' => $payment,
                        'total_payment' => $userPayment->total_payment + $paymentInDay
                    ]);
                }
            }else{
                UserPaymentByDay::create([
                    'user_uuid' => $userUuid,
                    'month' => $time->month,
                    'year' => $time->year,
                    'payment' => [
                        $time->day => $paymentInDay
                    ],
                    'total_payment' => $paymentInDay
                ]);
            }
        }
    }
}
