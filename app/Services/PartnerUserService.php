<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Company;
use App\Models\PartnerUser;
use App\Models\QueryBuilders\CompanyQueryBuilder;

class PartnerUserService extends AbstractService
{
    protected $modelClass = PartnerUser::class;

    //Customer là khách hàng có thanh toán ít nhất 1 lần
    //(dựa vào bảng subscription history và add on subscription history)
    public function customersPartner($partnerCode)
    {
        return $this->model->where('registered_from_partner_code', $partnerCode)
            ->where(function ($query) {
                $query->whereIn('partner_user.user_uuid', function ($q) {
                    $q->select('a.user_uuid')
                        ->from('add_on_subscription_histories as a')
                        ->whereColumn('a.user_uuid', 'partner_user.user_uuid');
                })->orWhereIn('partner_user.user_uuid', function ($q) {
                    $q->select('a.user_uuid')
                        ->from('subscription_histories as a')
                        ->whereColumn('a.user_uuid', 'partner_user.user_uuid');
                });
            })->get();
    }

    public function referrerStatisticsOfPartnerbyType($partnerCode, $type = null)
    {
        //Lấy danh sách user đã đăng ký từ partner (user_uuid, email, created_at, customer_since)
        $referredUsers = $this->model->join('users', 'users.uuid', '=', 'partner_user.user_uuid')
            ->where('registered_from_partner_code', $partnerCode)
            ->selectRaw('users.uuid as user_uuid, users.email, users.created_at, null as customer_since')
            ->get()->keyBy('user_uuid')->toArray();

        //Lấy danh sách user đã thanh toán gói đăng ký
        $subscriptionUsers = $this->model->join('users as u', 'u.uuid','=', 'partner_user.user_uuid')
            ->join('add_on_subscription_histories as a', 'a.user_uuid','=', 'partner_user.user_uuid')
            ->where('registered_from_partner_code', $partnerCode)->selectRaw("a.user_uuid, u.email, u.created_at , MIN(a.created_at) as customer_since")
            ->groupByRaw("a.user_uuid, u.email, u.created_at");

        //Lấy danh sách user đã thanh toán ít nhất một lần và lấy thời gian đầu tiên  (add_on_subscription_histories union subscription_histories)
        $customersPartner = $this->model->join('users as u', 'u.uuid','=', 'partner_user.user_uuid')
            ->join('subscription_histories as a', 'a.user_uuid','=', 'partner_user.user_uuid')
            ->where('registered_from_partner_code', $partnerCode)->selectRaw("a.user_uuid, u.email, u.created_at , MIN(a.created_at) as customer_since")
            ->groupByRaw("a.user_uuid, u.email, u.created_at")->union($subscriptionUsers)
            ->get()->sortBy('customer_since')->unique('user_uuid')->keyBy('user_uuid')->toArray();

        //Danh sách user cancel gói đăng ký
        $cancelSubscriptionUsers = $this->model->join('user_platform_package as a', 'a.user_uuid', '=', 'partner_user.user_uuid')
            ->where([
                ['registered_from_partner_code', $partnerCode],
                ['a.auto_renew', false],
                ['a.deleted_at', null]
            ])->select('a.user_uuid')->get()->pluck('user_uuid')->toArray();

        $results = $referredUsers;
        foreach ($referredUsers as $item) {
            if (array_key_exists($item['user_uuid'], $customersPartner)) {
                $results[$item['user_uuid']] = $customersPartner[$item['user_uuid']];
            }
        }

        if ($type === "free") {
            $results = array_filter($results, function ($item) {
                return is_null($item['customer_since']);
            });
        }
        if ($type === 'paying') {
            $results = array_filter($results, function ($item) use ($cancelSubscriptionUsers) {
                return !is_null($item['customer_since']) && !in_array($item['user_uuid'], $cancelSubscriptionUsers);
            });
        }
        if ($type === 'cancel') {
            $results = array_filter($results, function ($item) use ($cancelSubscriptionUsers) {
                return in_array($item['user_uuid'], $cancelSubscriptionUsers);
            });
        }

        return array_values($results);
    }

    public function subAffiliatesStatisticsOfPartner($partnerCode)
    {
        $referredUsers = $this->model->join('partners as a' , 'a.code' , '=' , 'partner_user.partner_code')
            ->where('registered_from_partner_code', $partnerCode)
            ->selectRaw("partner_user.user_uuid, concat(a.first_name, ' ', a.first_name) as full_name, partner_user.partner_code, partner_user.partnered_at")->get()->keyBy('user_uuid')->toArray();

        $results = $referredUsers;
        foreach ($referredUsers as $key => $value) {
            $numberCustomersByPartnerCode =  $this->customersPartner($value['partner_code'])->count();
            $results[$key] = [
                'user_uuid' => $value['user_uuid'],
                'name' => $value['full_name'],
                'joined' => $value['partnered_at'],
                'customers' => $numberCustomersByPartnerCode,
            ];
        }
        return array_values($results);
    }
}
