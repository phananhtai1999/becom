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
}
