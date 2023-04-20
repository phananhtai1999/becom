<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserPaymentByDay;

class UserPaymentByDayService extends AbstractService
{
    protected $modelClass = UserPaymentByDay::class;

    public function trackingCustomersByDate($startDate, $endDate, $partnerCode)
    {
        //Lấy danh sách user có thanh toán trong start -> endDate
        $payments = $this->model->join('partner_user as a', 'a.user_uuid', '=', 'user_payment_by_day.user_uuid')
            ->where('a.registered_from_partner_code', $partnerCode)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate) {
                    $q->where('user_payment_by_day.month', $startDate->month)
                        ->where('user_payment_by_day.year', $startDate->year);
                })->orWhere(function ($query) use ($endDate) {
                    $query->where('user_payment_by_day.month', $endDate->month)
                        ->where('user_payment_by_day.year', $endDate->year);
                });
            })->where(function ($query) use ($startDate, $endDate) {
                for ($day = $startDate->day; $day <= $endDate->day; $day++) {
                    $query->orWhereNotNull("user_payment_by_day.payment->$day");
                }
            })->select('user_payment_by_day.*')->get();

        //Tính commission dựa vào tháng và năm của start và endDate rồi xem thử partner tháng đó có bnhiu customer -> level -> commission
        $commissionByMonthOfStartDate = (new PartnerLevelService())->getPartnerLevelOfPartnerByMontYear($partnerCode, $startDate->month, $startDate->year)->commission;
        $commissionByMonthOfEndDate = (new PartnerLevelService())->getPartnerLevelOfPartnerByMontYear($partnerCode, $endDate->month, $endDate->year)->commission;

        // Đếm số lượng user thanh toán theo ngày có số tiền và hoa hồng
        $countByDate = [];
        foreach ($payments as $payment) {
            $commission = ($payment->month === $startDate->month ? $commissionByMonthOfStartDate : $commissionByMonthOfEndDate);
            foreach ($payment->payment as $day => $amount) {
                $date = date('Y-m-d', strtotime($payment->year . '-' . $payment->month . '-' . $day));
                if ($date >= $startDate && $date <= $endDate) {
                    if (isset($countByDate[$date])) {
                        $countByDate[$date]['customers'] ++;
                        $countByDate[$date]['amount'] += $amount;
                        $countByDate[$date]['earnings'] = $countByDate[$date]['amount'] * $commission / 100;
                    } else {
                        $countByDate[$date] = [
                            'label' => $date,
                            'customers' => 1,
                            'amount' => $amount,
                            'earnings' => $amount * $commission / 100
                        ];
                    }
                }
            }
        }
        return $countByDate;
    }

    public function trackingCustomersByMonth($startDate, $endDate, $partnerCode)
    {
        $payments = $this->model->join('partner_user as a', 'a.user_uuid', '=', 'user_payment_by_day.user_uuid')
            ->where('a.registered_from_partner_code', $partnerCode)
            ->whereDate('user_payment_by_day.created_at', '>=', $startDate)
            ->whereDate('user_payment_by_day.updated_at', '<=', $endDate)
            ->select('user_payment_by_day.*')->get()->groupBy(function ($item){
                return date('Y-m', strtotime($item->created_at));
            })->map(function ($item, $key) {
                return [
                    'label' => $key,
                    'customers' => $item->count(),
                    'amount' => $item->sum('total_payment'),
                ];
            });
        return $payments->values();
    }

    public function trackingCustomersByWeek($startDate, $endDate, $partnerCode)
    {
        //Đếm số lượng user thanh toán theo ngày (4 week)
        $countByDay = $this->trackingCustomersByDate($startDate, $endDate, $partnerCode);

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
}
