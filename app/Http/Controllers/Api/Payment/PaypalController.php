<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Events\SendNotificationSystemForPaymentEvent;
use App\Events\SubscriptionAddOnSuccessEvent;
use App\Events\SubscriptionSuccessEvent;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Services\AddOnSubscriptionPlanService;
use App\Services\CreditPackageService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\PaypalService;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use PayPal\Api\Payment;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;


class PaypalController extends AbstractRestAPIController
{
    public function __construct(
        PaypalService $service,
        PaymentService $paymentService,
        AddOnSubscriptionPlanService $addOnSubscriptionPlanService,
        SubscriptionPlanService $subscriptionPlanService,
        CreditPackageService $creditPackageService,
        InvoiceService $invoiceService
    ) {
        $this->service = $service;
        $this->paymentService = $paymentService;
        $this->addOnSubscriptionPlanService = $addOnSubscriptionPlanService;
        $this->subscriptionPlanService = $subscriptionPlanService;
        $this->creditPackageService = $creditPackageService;
        $this->invoiceService = $invoiceService;
    }
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancelPayment(Request $request)
    {

        return $this->paymentService->cancelPaymentUrl($request);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function successPayment(Request $request)
    {
        $provider = $this->service->accessServer();
        $response = $provider->capturePaymentOrder($request['token']);
        $paymentData = [
            "token" => $request['token'],
            "payerId" => $request['PayerID'],
        ];
        $creditPackage = $this->creditPackageService->findOrFailById($request->creditPackageUuid);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $invoiceData = $this->paymentService->getInvoiceDataForCreditPackage($request, $creditPackage, PaymentMethod::PAYPAL);
            $invoice = $this->invoiceService->create(array_merge($invoiceData, ['app_id' => auth()->appId()]));
            Event::dispatch(new PaymentCreditPackageSuccessEvent($request->creditPackageUuid, $paymentData, $request->userUuid, PaymentMethod::PAYPAL, $invoice));
            Event::dispatch(new SendNotificationSystemForPaymentEvent([
                'credit_package_uuid' => $request->creditPackageUuid,
                'user_uuid' => $request->userUuid,
                'app_id' => $request->app_id,
                'payment_method_uuid' => PaymentMethod::PAYPAL
            ], Notification::CREDIT_TYPE));

            return $this->paymentService->successPaymentUrl($request, $invoice);
        } else {

            return $this->paymentService->failedPaymentUrl($request);
        }
    }

    public function successPaymentSubscription(Request $request)
    {
        $provider = $this->service->accessServer();
        $provider->updateSubscription($request['subscription_id'], ["start_time" => Carbon::now()->addMinute(1)]);

        $response = $provider->showSubscriptionDetails($request['subscription_id']);
        $subscriptionData = ["id" => $response['id']];

        $subscriptionHistoryData = $this->paymentService->getSubscriptionHistoryData($request, PaymentMethod::PAYPAL, $subscriptionData);

        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            $userPlatformPackageData = $this->paymentService->getUserPlatformPackageData($request);
            $subscriptionPlan = $this->subscriptionPlanService->findOrFailById($request->subscriptionPlanUuid);
            $invoiceData = $this->paymentService->getInvoiceDataForPlatformPackage($request, $subscriptionPlan, PaymentMethod::STRIPE);
            $invoice = $this->invoiceService->create(array_merge($invoiceData, ['app_id' => auth()->appId()]));

            Event::dispatch(new SubscriptionSuccessEvent($request->userUuid, $subscriptionHistoryData, $userPlatformPackageData, $invoice));
            Event::dispatch(new SendNotificationSystemForPaymentEvent($subscriptionHistoryData, Notification::PACKAGE_TYPE));

            return $this->paymentService->successPaymentSubscriptionUrl($request, $invoice);
        } else {

            return $this->paymentService->failedPaymentSubscriptionUrl($request);
        }
    }

    public function cancelPaymentSubscription(Request $request)
    {
        return $this->paymentService->cancelPaymentSubscriptionUrl($request);
    }

    public function successPaymentSubscriptionAddOn(Request $request)
    {
        $provider = $this->service->accessServer();
        $provider->updateSubscription($request['subscription_id'], ["start_time" => Carbon::now()->addMinute(1)]);

        $response = $provider->showSubscriptionDetails($request['subscription_id']);
        $subscriptionData = ["id" => $response['id']];

        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            $addOnSubscriptionHistoryData = $this->paymentService->getAddOnSubscriptionHistoryData($request, PaymentMethod::PAYPAL, $subscriptionData);
            $userAddOnData = $this->paymentService->getUserAddOnData($request);
            $addOnSubscriptionPlan = $this->addOnSubscriptionPlanService->findOrFailById($request->addOnSubscriptionPlanUuid);
            $invoiceData = $this->paymentService->getInvoiceDataForAddOn($request, $addOnSubscriptionPlan, PaymentMethod::PAYPAL);
            $invoice = $this->invoiceService->create(array_merge($invoiceData, ['app_id' => auth()->appId()]));
            Event::dispatch(new SubscriptionAddOnSuccessEvent($request->userUuid, $addOnSubscriptionHistoryData, $userAddOnData, $invoice));
            Event::dispatch(new SendNotificationSystemForPaymentEvent($addOnSubscriptionHistoryData, Notification::ADDON_TYPE));

            return $this->paymentService->successPaymentSubscriptionAddOnUrl($request, $invoice);
        } else {

            return $this->paymentService->failedPaymentSubscriptionAddOnUrl($request);
        }
    }

    public function cancelPaymentSubscriptionAddOn(Request $request)
    {
        return $this->paymentService->cancelPaymentSubscriptionAddOnUrl($request);
    }
}
