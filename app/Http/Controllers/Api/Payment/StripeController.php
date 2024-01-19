<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Events\SendNotificationSystemForPaymentEvent;
use App\Events\SubscriptionAddOnSuccessEvent;
use App\Events\SubscriptionSuccessEvent;
use App\Http\Requests\UpdateCardCustomerRequest;
use App\Http\Requests\UpdateCardStripeRequest;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Services\AddOnSubscriptionPlanService;
use App\Services\CreditPackageService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\StripeService;
use App\Services\SubscriptionHistoryService;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class StripeController extends AbstractRestAPIController
{

    const UPDATE_CARD_STRIPE = 'update';
    const ADD_CARD_STRIPE = 'add';

    public function __construct(
        StripeService              $service,
        SubscriptionHistoryService $subscriptionHistoryService,
        PaymentService $paymentService,
        AddOnSubscriptionPlanService $addOnSubscriptionPlanService,
        SubscriptionPlanService $subscriptionPlanService,
        CreditPackageService $creditPackageService,
        InvoiceService $invoiceService
    )
    {
        $this->service = $service;
        $this->paymentService = $paymentService;
        $this->subscriptionHistoryService = $subscriptionHistoryService;
        $this->addOnSubscriptionPlanService = $addOnSubscriptionPlanService;
        $this->subscriptionPlanService = $subscriptionPlanService;
        $this->creditPackageService = $creditPackageService;
        $this->invoiceService = $invoiceService;
    }

    public function cardStripe(UpdateCardCustomerRequest $request)
    {
        try {
            $customer = $this->getCustomer();
            $token = $this->service->createNewToken($request);
            if ($request->get('type') == self::UPDATE_CARD_STRIPE) {
                $this->service->updateCustomerCard($customer->id, $token);
                $message = 'Update Card Successfully';
            } else {
                $this->service->addCard($customer->id, $token);
                $message = 'Add new Card Successfully';
            }
            $response = $this->sendOkJsonResponse(['message' => $message]);

        } catch (\Exception $exception) {
            $response = $this->sendJsonResponse(false, $exception->getMessage(), [], 400);
        }

        return $response;
    }

    public function allCardStripe()
    {
        $subscriptionHistory = $this->subscriptionHistoryService->findOneWhere([
            'payment_method_uuid' => PaymentMethod::STRIPE,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);

        if (empty($subscriptionHistory)) {
            return $this->sendOkJsonResponse(['message' => 'Does not have card before']);
        }

        $stripe = $this->service->getStripeClient();
        $subscription = $stripe->subscriptions->retrieve($subscriptionHistory->logs['id']);
        $paymentMethods = $stripe->paymentMethods->all([
            'customer' => $subscription->customer,
            'type' => 'card',
        ]);
        foreach ($paymentMethods->data as $paymentMethod) {
            $card[] = $paymentMethod['card'];
        }
        return $this->sendOkJsonResponse(['data' => $card]);
    }

    public function updateCard(UpdateCardStripeRequest $request, $id)
    {
        try {
            $customer = $this->getCustomer();
            $card = $this->service->updateOneCard($customer->id, $request, $id);
            $response = $this->sendOkJsonResponse(['data' => $card]);
        } catch (\Exception $exception) {
            $response = $this->sendJsonResponse(false, $exception->getMessage(), [], 400);
        }

        return $response;
    }

    public function getCustomer()
    {
        $subscriptionHistory = $this->subscriptionHistoryService->findOneWhere([
            'payment_method_uuid' => PaymentMethod::STRIPE,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);
        if (empty($subscriptionHistory)) {
            return $this->sendOkJsonResponse(['message' => 'Does not have card before']);
        }
        $stripe = $this->service->getStripeClient();
        $subscription = $stripe->subscriptions->retrieve($subscriptionHistory->logs['id']);

        return $stripe->customers->retrieve($subscription->customer);
    }

    public function successPaymentSubscription(Request $request)
    {
        $stripe = $this->service->getStripeClient();
        $response = $stripe->checkout->sessions->retrieve($request->session_id);
        $subscriptionData = ["id" => $response->subscription];

        if (isset($response['status']) && $response['status'] == 'complete') {
            $subscriptionHistoryData = $this->paymentService->getSubscriptionHistoryData($request, PaymentMethod::STRIPE, $subscriptionData);
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
        $stripe = $this->service->getStripeClient();
        $response = $stripe->checkout->sessions->retrieve($request->session_id);
        $subscriptionData = ["id" => $response->subscription];

        $addOnSubscriptionHistoryData = $this->paymentService->getAddOnSubscriptionHistoryData($request, PaymentMethod::STRIPE, $subscriptionData);
        if (isset($response['status']) && $response['status'] == 'complete') {
            $userAddOnData = $this->paymentService->getUserAddOnData($request);
            $addOnSubscriptionPlan = $this->addOnSubscriptionPlanService->findOrFailById($request->addOnSubscriptionPlanUuid);
            $invoiceData = $this->paymentService->getInvoiceDataForAddOn($request, $addOnSubscriptionPlan, PaymentMethod::STRIPE);
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

    public function cancelPayment(Request $request)
    {

        return $this->paymentService->cancelPaymentUrl($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function successPayment(Request $request)
    {
        $stripe = $this->service->getStripeClient();
        $response = $stripe->checkout->sessions->retrieve($request->session_id);
        $paymentData = [
            "token" => $response->payment_intent
        ];
        $creditPackage = $this->creditPackageService->findOrFailById($request->creditPackageUuid);


        if (isset($response['status']) && $response['status'] == 'complete') {
            $invoiceData = $this->paymentService->getInvoiceDataForCreditPackage($request, $creditPackage, PaymentMethod::STRIPE);
            $invoice = $this->invoiceService->create(array_merge($invoiceData, ['app_id' => auth()->appId()]));
            Event::dispatch(new PaymentCreditPackageSuccessEvent($request->creditPackageUuid, $paymentData, $request->userUuid, PaymentMethod::STRIPE, $invoice));
            Event::dispatch(new SendNotificationSystemForPaymentEvent([
                'credit_package_uuid' => $request->creditPackageUuid,
                'user_uuid' => $request->userUuid,
                'app_id' => auth()->appId(),
                'payment_method_uuid' => PaymentMethod::STRIPE
            ], Notification::CREDIT_TYPE));

            return $this->paymentService->successPaymentUrl($request, $invoice);
        } else {

            return $this->paymentService->failedPaymentUrl($request);
        }
    }
}
