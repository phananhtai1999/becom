<?php

namespace App\Console\Commands;

use App\Models\AddOnSubscriptionHistory;
use App\Models\CreditPackageHistory;
use App\Models\Partner;
use App\Models\PartnerTrackingByYear;
use App\Models\PartnerUser;
use App\Models\SubscriptionHistory;
use App\Models\UserPaymentByDay;
use App\Services\PartnerLevelService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UpdatePartnerTrackingByYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:partner-tracking';

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
        $subMonth = Carbon::today()->startOfMonth()->subMonth();
        $subMonthFormat = $subMonth->format('Y-m');
        if (!Redis::get($subMonthFormat)){
            $this->updatePartnerTrackingByMonth($subMonth);
            Redis::set($subMonthFormat, true);
        }
        if(Redis::get($subMonthFormat) && $today->isLastOfMonth()){
            Redis::set($today->format('Y-m'), false);
            $this->updatePartnerTrackingByMonth($today);
            Redis::set($today->format('Y-m'), true);
        }
    }

    public function updatePartnerTrackingByMonth($time)
    {
        $usersPartner = PartnerUser::whereNotNull('registered_from_partner_code')->whereIn('partner_user.user_uuid', function ($query) use ($time) {
            $query->select('a.user_uuid')
                ->from('user_payment_by_day as a')
                ->whereColumn('a.user_uuid', 'partner_user.user_uuid')
                ->where('a.month', $time->month)
                ->where('a.year', $time->year);
        })->get()->groupBy('registered_from_partner_code')->map(function ($customers, $key) use ($time){
            $commission = (new PartnerLevelService())->getPartnerLevelOfPartnerByMontYear($key, $time->month, $time->year)->commission;
            $totalAmount = 0;
            foreach ($customers as $customer){
                $userPayment = UserPaymentByDay::where([
                    ['user_uuid', $customer->user_uuid],
                    ['month', $time->month],
                    ['year', $time->year],
                ])->first();
                $totalAmount += optional($userPayment)->total_payment;
            }
            return [
                'partner_uuid' => Partner::where('code', $key)->withTrashed()->first()->uuid,
                'commission' => $totalAmount * $commission / 100
            ];
        });

        foreach ($usersPartner as $userPartner){
            $partnerEarning = PartnerTrackingByYear::where([
                ['partner_uuid', $userPartner['partner_uuid']],
                ['year', $time->year]
            ])->first();
            if ($partnerEarning){
                $earning = $partnerEarning->commission;
                if (!array_key_exists($time->month, $earning)) {
                    $earning[$time->month] = $userPartner['commission'];
                    $partnerEarning->update([
                        'commission' => $earning,
                        'total_commission' => $partnerEarning->total_commission + $userPartner['commission']
                    ]);
                }else{
                    $total_earning_remaining = $userPartner['commission'] - $earning[$time->month];
                    $earning[$time->month] = $userPartner['commission'];
                    $partnerEarning->update([
                        'commission' => $earning,
                        'total_commission' => $partnerEarning->total_commission + $total_earning_remaining
                    ]);
                }
            }else{
                PartnerTrackingByYear::create([
                    'partner_uuid' => $userPartner['partner_uuid'],
                    'year' => $time->year,
                    'commission' => [
                        $time->month => $userPartner['commission']
                    ],
                    'total_commission' => $userPartner['commission']
                ]);
            }
        }
    }
}
