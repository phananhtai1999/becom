<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AddOn;
use App\Models\AddOnSubscriptionPlan;
use App\Models\PlatformPackage;

class PaymentService extends AbstractService
{
    public function getSubscriptionHistoryData($request, $paymentMethod, $subscriptionData) {
        return [
            'user_uuid' => $request->userUuid,
            'app_id' => auth()->appId(),
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
            'app_id' => auth()->appId(),
            'platform_package_uuid' => $request->platformPackageUuid,
            'subscription_plan_uuid' => $request->subscriptionPlanUuid,
            'expiration_date' => $request->expirationDate,
            'auto_renew' => true
        ];
    }

    public function getAddOnSubscriptionHistoryData($request, $paymentMethod, $subscriptionData) {
        return [
            'user_uuid' => $request->userUuid,
            'app_id' => auth()->appId(),
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
            'app_id' => auth()->appId(),
            'add_on_subscription_plan_uuid' => $request->addOnSubscriptionPlanUuid,
            'expiration_date' => $request->expirationDate,
            'auto_renew' => true
        ];
    }

    public function getInvoiceDataForAddOn($request, $addOnSubscriptionPlan, $paymentMethod)
    {
        if ($addOnSubscriptionPlan->duration_type == AddOn::ADD_ON_DURATION_MONTH) {
            $price = $addOnSubscriptionPlan->addOn->monthly;
        }else {
            $price = $addOnSubscriptionPlan->addOn->yearly;
        }
        $productData = [
            'type' => 'Subscription',
            'name' => $addOnSubscriptionPlan->addOn->name . ' add-on',
            'quantity' => 1,
            'duration' => join(' ', [$addOnSubscriptionPlan->duration, $addOnSubscriptionPlan->duration_type]),
            'price' => $price
        ];

        return $this->getInvoiceData($request, $productData, $paymentMethod);
    }

    public function getInvoiceDataForPlatformPackage($request, $subscriptionPlan, $paymentMethod)
    {
        if ($subscriptionPlan->duration_type == PlatformPackage::DURATION_MONTH) {
            $price = $subscriptionPlan->platformPackage->monthly;
        }else {
            $price = $subscriptionPlan->platformPackage->yearly;
        }
        $productData = [
            'type' => 'Subscription',
            'name' => $subscriptionPlan->platformPackage->uuid . ' platform package',
            'quantity' => 1,
            'duration' => join(' ', [$subscriptionPlan->duration, $subscriptionPlan->duration_type]),
            'price' => $price
        ];

        return $this->getInvoiceData($request, $productData, $paymentMethod);
    }


    public function getInvoiceDataForCreditPackage($request, $creditPackage, $paymentMethod)
    {
        $productData = [
            'type' => 'Payment',
            'name' => $creditPackage->credit . ' credit',
            'quantity' => 1,
            'duration' => '1 time',
            'price' => $creditPackage->price
        ];

        return $this->getInvoiceData($request, $productData, $paymentMethod);
    }

    public function getInvoiceData($request, $productData, $paymentMethod) {

        return [
            'billing_address_uuid' => $request->billingAddressUuid,
            'user_uuid' => $request->userUuid,
            'app_id' => auth()->appId(),
            'product_data' => $productData,
            'payment_method_uuid' => $paymentMethod
        ];
    }

    //subscription platform package
    public function cancelPaymentSubscriptionUrl($request) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/canceled?go_back_url=' . $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid);
    }

    public function successPaymentSubscriptionUrl($request, $invoice) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/success?go_back_url=' . $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid . '&invoice_id=' . $invoice->uuid);
    }

    public function failedPaymentSubscriptionUrl($request) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/failed?go_back_url=' . $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid);
    }

    //subscription add-ons
    public function cancelPaymentSubscriptionAddOnUrl($request) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/canceled?go_back_url=' . $request['goBackUrl'] . '&addOnUuid=' . $request['addOnUuid']);
    }

    public function successPaymentSubscriptionAddOnUrl($request, $invoice) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/success?go_back_url=' . $request['goBackUrl'] . '&addOnSubscriptionPlanUuid=' . $request->addOnSubscriptionPlanUuid . '&invoice_id=' . $invoice->uuid);
    }

    public function failedPaymentSubscriptionAddOnUrl($request) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/failed?go_back_url=' . $request['goBackUrl'] . '&addOnSubscriptionPlanUuid=' . $request->addOnSubscriptionPlanUuid);
    }

    //payment credit package
    public function cancelPaymentUrl($request) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/cancel?packageID=' . $request->creditPackageUuid);
    }

    public function successPaymentUrl($request, $invoice) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/success?go_back_url=' . $request->goBackUrl . '&package_id=' . $request->creditPackageUuid . '&invoice_id=' . $invoice->uuid);
    }

    public function failedPaymentUrl($request) {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/failed?go_back_url=' . $request->goBackUrl . '&package_id=' . $request->creditPackageUuid);
    }
}
