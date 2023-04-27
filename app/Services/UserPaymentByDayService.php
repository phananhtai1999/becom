<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserPaymentByDay;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UserPaymentByDayService extends AbstractService
{
    protected $modelClass = UserPaymentByDay::class;

    public function createQueryGetCustomersPartnerByDate($startDate, $endDate, $partnerCode)
    {
        return $this->model
            ->join('partner_user as a', 'a.user_uuid', '=', 'user_payment_by_day.user_uuid')
            ->when($partnerCode, function ($query, $partnerCode) {
                $query->where('registered_from_partner_code', $partnerCode);
            })
            ->whereNotNull('a.registered_from_partner_code')
            ->whereRaw("CONCAT(user_payment_by_day.year, '-', LPAD(user_payment_by_day.month, 2, '0'), '-01') BETWEEN '{$startDate->copy()->startOfMonth()->toDateString()}' AND '{$endDate->toDateString()}'")
            ->select('user_payment_by_day.*', 'a.registered_from_partner_code')->get();
    }

    public function getCustomersPartnerByMonth($startDate, $endDate, $partnerCode)
    {
        $payments = $this->createQueryGetCustomersPartnerByDate($startDate, $endDate, $partnerCode);
        return $payments->groupBy(function ($item) {
           return date('Y-m', strtotime($item->year.'-'.$item->month));
        })->map(function ($item, $yearMonth) {
            $earningsPartnerByMonth = $item->groupBy('registered_from_partner_code')->map(function ($item, $partnerCode) use ($yearMonth) {
                $time = Carbon::parse($yearMonth);
                $commissionMonthByPartner = (new PartnerLevelService())->getCommissionByTimeOfPartner($time, $partnerCode);
                return $item->sum('total_payment') * $commissionMonthByPartner / 100;
            });
            return [
                'label' => $yearMonth,
                'customers' => $item->count(),
                'amount' => $item->sum('total_payment'),
                'earnings' => $earningsPartnerByMonth->sum()
            ];
        });
    }

    public function getCustomersPartnerByDate($startDate, $endDate, $partnerCode)
    {
        //Lấy danh sách user có thanh toán trong start -> endDate
        $payments = $this->createQueryGetCustomersPartnerByDate($startDate, $endDate, $partnerCode);

        $countByDate = [];
        foreach ($payments as $payment) {
            $time = Carbon::parse($payment->year . '-' . $payment->month);
            $commissionMonthByPartner = (new PartnerLevelService())->getCommissionByTimeOfPartner($time, $payment->registered_from_partner_code);
            foreach ($payment->payment as $day => $amount) {
                $date = date('Y-m-d', strtotime($payment->year . '-' . $payment->month . '-' . $day));
                if ($date >= $startDate->toDateString() && $date <= $endDate->toDateString()) {
                    if (isset($countByDate[$date])) {
                        $countByDate[$date]['customers']++;
                        $countByDate[$date]['amount'] += $amount;
                        $countByDate[$date]['earnings'] += ($amount * $commissionMonthByPartner / 100);
                    } else {
                        $countByDate[$date] = [
                            'label' => $date,
                            'customers' => 1,
                            'amount' => $amount,
                            'earnings' => $amount * $commissionMonthByPartner / 100
                        ];
                    }
                }
            }
        }
        return $countByDate;
    }

    public function trackingCustomersByWeek($startDate, $endDate, $partnerCode)
    {
        //Đếm số lượng user thanh toán theo ngày (4 week)
        $countByDay = $this->getCustomersPartnerByDate($startDate, $endDate, $partnerCode);

        $results = [];
        foreach ($countByDay as $date => $item) {
            $week = date('Y-W', strtotime($date));
            if (isset($results[$week])) {
                $results[$week]['customers'] += $item['customers'];
                $results[$week]['amount'] += $item['amount'];
                $results[$week]['earnings'] += $item['earnings'];
            }else {
                $results[$week] = [
                    'label' => $week,
                    'customers' => $item['customers'],
                    'amount' => $item['amount'],
                    'earnings' => $item['earnings'],
                ];
            }
        }

        return array_values($results);
    }

    public function getRewardsCommissionThisMonthByPartner($partnerCode)
    {
        $today = Carbon::today();
        $payments = $this->model->with('user')->join('partner_user as a', 'a.user_uuid', '=', 'user_payment_by_day.user_uuid')
            ->where([
                ['a.registered_from_partner_code', $partnerCode],
                ['user_payment_by_day.month', $today->month],
                ['user_payment_by_day.year', $today->year],
            ])
            ->select('user_payment_by_day.*')->get();

        $commission = (new PartnerLevelService())->getCommissionByTimeOfPartner($today, $partnerCode);
        $result = [];
        foreach ($payments as $payment) {
            foreach ($payment->payment as $day => $amount){
                $date = date('Y-m-d', strtotime($payment->year . '-' . $payment->month . '-' . $day));
                $email = substr($payment->user->email, 0, 5) . str_repeat('*', strlen($payment->user->email) - 10) . substr($payment->user->email, -5);
                $result[] = [
                    'from_customer' => $email,
                    'amount' => $amount,
                    'earning' => $amount * $commission / 100,
                    'created' => $date,
                    'status' => 'unpaid'
                ];
            }
        }

        return collect($result)->sortByDesc('created')->values();
    }

    public function getCustomersChartByGroup($startDate, $endDate, $groupBy, $partnerCode)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $times = [];
        $result = [];
        if ($groupBy == "date"){
            $charts = $this->getCustomersPartnerByDate($startDate, $endDate, $partnerCode);

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->addDay();
            }
        }

        if ($groupBy == "month"){
            $charts = $this->getCustomersPartnerByMonth($startDate, $endDate, $partnerCode)->toArray();

            $period = CarbonPeriod::create($startDate->format('Y-m'), '1 month', $endDate->format('Y-m'));
            foreach ($period as $date) {
                $times[] = $date->format('Y-m');
            }
        }

        foreach ($times as $time) {
            $chart = array_filter($charts, function ($key) use ($time){
                return $key === $time;
            },ARRAY_FILTER_USE_KEY);
            if ($chart){
                $result[] = [
                    'label' => $time,
                    'customers'  => $chart[$time]['customers'],
                    'amount'  => $chart[$time]['amount'],
                ];
            }else{
                $result [] = [
                    'label' => $time,
                    'customers'  => 0,
                    'amount'  => 0,
                ];
            }
        }

        return $result;
    }

    public function getEarningsChartByGroup($startDate, $endDate, $groupBy, $partnerCode)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $times = [];
        $result = [];
        if ($groupBy == "date"){
            $charts = $this->getCustomersPartnerByDate($startDate, $endDate, $partnerCode);

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->addDay();
            }
        }

        if ($groupBy == "month"){
            $charts = $this->getCustomersPartnerByMonth($startDate, $endDate, $partnerCode)->toArray();

            $period = CarbonPeriod::create($startDate->format('Y-m'), '1 month', $endDate->format('Y-m'));
            foreach ($period as $date) {
                $times[] = $date->format('Y-m');
            }
        }

        foreach ($times as $time) {
            $chart = array_filter($charts, function ($key) use ($time){
                return $key === $time;
            },ARRAY_FILTER_USE_KEY);
            if ($chart){
                $result[] = [
                    'label' => $time,
                    'earnings'  => $chart[$time]['earnings'],
                ];
            }else{
                $result [] = [
                    'label' => $time,
                    'earnings'  => 0,
                ];
            }
        }

        return $result;
    }
}
