<?php

namespace App\Services;

use App\Abstracts\AbstractService;

class PaymentService extends AbstractService
{
    public function getSubscriptionHistoryData($request, $paymentMethod, $subscriptionData) {
        return [
            'user_uuid' => $request->userUuid,
            'subscription_plan_uuid' => $request->subscriptionPlanUuid,
            'subscription_date' => $request->subscriptionDate,
            'billing_address_uuid' => $request->billingAddressUuid,
            'expiration_date' => $request->expirationDate,
            'payment_method_uuid' => $paymentMethod,
            'logs' => $subscriptionData,
            'status' => 'success'
        ];
    }

    public function getUserPlatformPackageData($request)
    {
        return [
            'user_uuid' => $request->userUuid,
            'platform_package_uuid' => $request->platformPackageUuid,
            'subscription_plan_uuid' => $request->subscriptionPlanUuid,
            'expiration_date' => $request->expirationDate,
            'auto_renew' => true
        ];
    }

    public function getAddOnSubscriptionHistoryData($request, $paymentMethod, $subscriptionData) {
        return [
            'user_uuid' => $request->userUuid,
            'add_on_subscription_plan_uuid' => $request->addOnSubscriptionPlanUuid,
            'subscription_date' => $request->subscriptionDate,
            'billing_address_uuid' => $request->billingAddressUuid,
            'expiration_date' => $request->expirationDate,
            'payment_method_uuid' => $paymentMethod,
            'logs' => $subscriptionData,
        ];
    }

    public function getUserAddOnData($request)
    {
        return [
            'user_uuid' => $request->userUuid,
            'add_on_subscription_plan_uuid' => $request->addOnSubscriptionPlanUuid,
            'expiration_date' => $request->expirationDate,
            'auto_renew' => true
        ];
    }
}
