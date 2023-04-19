<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Company;
use App\Models\PartnerUser;
use App\Models\QueryBuilders\CompanyQueryBuilder;
use App\Models\UserPaymentByDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PartnerUserService extends AbstractService
{
    protected $modelClass = PartnerUser::class;

    //Customer là khách hàng có thanh toán ít nhất 1 lần
    //(dựa vào bảng subscription history và add on subscription history)
    public function customersPartner($partnerCode)
    {
        return $this->model->where('registered_from_partner_code', $partnerCode)
            ->whereIn('partner_user.user_uuid', function ($query) {
                $query->select('a.user_uuid')
                    ->from('user_payment_by_day as a')
                    ->whereColumn('a.user_uuid', 'partner_user.user_uuid')
                    ->whereRaw('CURDATE() - date(a.updated_at) < 30');
            })->get();
    }

    public function numberCustomerPartnerInMonth($partnerCode)
    {
        //Get customer của partner theo tháng và năm
        return $this->model->where('registered_from_partner_code', $partnerCode)
            ->whereIn('partner_user.user_uuid', function ($query) {
                $query->select('a.user_uuid')
                    ->from('user_payment_by_day as a')
                    ->whereColumn('a.user_uuid', 'partner_user.user_uuid')
                    ->where('a.month', Carbon::today()->month)
                    ->where('a.year', Carbon::today()->year);
            })->get();
    }

    public function referralsOfPartnerInMonth($partnerCode)
    {
        return $this->model->where('registered_from_partner_code', $partnerCode)
            ->whereMonth('created_at', Carbon::today()->month)
            ->whereYear('created_at', Carbon::today()->year)->get();
    }

    public function referrerStatisticsOfPartnerbyType($partnerCode, $type = null)
    {

        $referredUsers = $this->model->join('users', 'users.uuid', '=', 'partner_user.user_uuid')
            ->where('partner_user.registered_from_partner_code', $partnerCode)
            ->selectRaw('users.uuid as user_uuid, users.email, users.created_at, null as customer_since, null as last_payment')
            ->get()->keyBy('user_uuid')->toArray();

        $customerSince = $this->model
            ->join('user_payment_by_day as a', 'a.user_uuid', '=', 'partner_user.user_uuid')
            ->where('partner_user.registered_from_partner_code', $partnerCode)
            ->selectRaw("a.user_uuid, a.created_at, IF(CURDATE() - date(a.updated_at) < 30, a.updated_at, null) as last_payment")
            ->get()->groupBy('user_uuid')->map(function ($item) {
                return [
                  'customer_since' => $item->min('created_at'),
                  'last_payment' => $item->max('last_payment'),
                ];
            })->toArray();

        $results = $referredUsers;

        foreach ($referredUsers as $user) {
            $results [$user['user_uuid']]['email'] = substr($user['email'], 0, 5) . str_repeat('*', strlen($user['email']) - 10) . substr($user['email'], -5);
            if (array_key_exists($user['user_uuid'], $customerSince)){
                $results [$user['user_uuid']]['customer_since'] = $customerSince[$user['user_uuid']]['customer_since'];
                $results [$user['user_uuid']]['last_payment'] = $customerSince[$user['user_uuid']]['last_payment'];
            }
        }

        if ($type === "free") {
            $results = array_filter($results, function ($item) {
                return is_null($item['customer_since']) || is_null($item['last_payment']);
            });
        }
        if ($type === 'paying') {
            $results = array_filter($results, function ($item){
                return !is_null($item['last_payment']);
            });
        }
        if ($type === 'cancel') {
            $results = array_filter($results, function ($item) {
                return !is_null($item['customer_since']) && is_null($item['last_payment']);
            });
        }

        return array_values($results);
    }

    public function subAffiliatesStatisticsOfPartner($partnerCode)
    {
        return $this->model->join('partners as a' , 'a.code' , '=' , 'partner_user.partner_code')
            ->where('registered_from_partner_code', $partnerCode)
            ->selectRaw("partner_user.user_uuid, concat(a.first_name, ' ', a.last_name) as full_name, partner_user.partner_code, partner_user.partnered_at")
            ->get()->map(function ($item) {
                $numberCustomersByPartnerCode =  $this->customersPartner($item->partner_code)->count();
                return [
                    'user_uuid' => $item->user_uuid,
                    'name' => $item->full_name,
                    'joined' => $item->partnered_at,
                    'customers' => $numberCustomersByPartnerCode,
                ];
            });
    }

    public function getTop10PartnerSignUp()
    {
        return $this->model->join('partners as a', 'a.code', '=', 'partner_user.registered_from_partner_code')
            ->selectRaw("count(partner_user.registered_from_partner_code) as count, concat(a.first_name, ' ', a.last_name) as full_name, a.partner_email")
            ->orderBy('count', 'DESC')->groupByRaw("full_name, a.partner_email")
            ->skip(0)->take(10)->dd();
    }

    public function getTop10PartnerCustomer()
    {
        return $this->model->join('partners as a', 'a.code', '=', 'partner_user.registered_from_partner_code')
            ->join('user_payment_by_day as u', 'u.user_uuid', '=' , 'partner_user.user_uuid')
            ->selectRaw("count(DISTINCT u.user_uuid) as count, concat(a.first_name, ' ', a.last_name) as full_name, a.partner_email")
            ->whereRaw('CURDATE() - date(u.updated_at) < 30')
            ->orderBy('count', 'DESC')->groupByRaw("full_name, a.partner_email")
            ->skip(0)->take(10)->get();
    }

    public function trackingSignUpByDateFormat($dateFormat, $startDate, $endDate, $partnerCode)
    {
        return $this->model->selectRaw("date_format(created_at, '{$dateFormat}') as label, count(uuid) as count")
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('registered_from_partner_code', $partnerCode)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }

    public function trackingCustomersByDateFormat($dateFormat, $startDate, $endDate, $partnerCode)
    {

    }
}
