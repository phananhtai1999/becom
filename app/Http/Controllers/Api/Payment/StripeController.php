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
        $customer = $this->getCustomer();
        $allCard = $this->service->allCard($customer->id);

        return $this->sendOkJsonResponse(['data' => $allCard]);
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
            'user_uuid' => auth()->user()->getKey()
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
            $invoice = $this->invoiceService->create($invoiceData);

            Event::dispatch(new SubscriptionSuccessEvent($request->userUuid, $subscriptionHistoryData, $userPlatformPackageData, $invoice));
            Event::dispatch(new SendNotificationSystemForPaymentEvent($subscriptionHistoryData, Notification::PACKAGE_TYPE));

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/success?go_back_url=' . $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid . '&invoice_id=' . $invoice->uuid);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/failed?go_back_url=' . $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid);
        }
    }

    public function cancelPaymentSubscription(Request $request)
    {
        return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/canceled?go_back_url=' . $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid);
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
            $invoice = $this->invoiceService->create($invoiceData);

            Event::dispatch(new SubscriptionAddOnSuccessEvent($request->userUuid, $addOnSubscriptionHistoryData, $userAddOnData, $invoice));
            Event::dispatch(new SendNotificationSystemForPaymentEvent($addOnSubscriptionHistoryData, Notification::ADDON_TYPE));

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/success?go_back_url=' . $request['goBackUrl'] . '&addOnSubscriptionPlanUuid=' . $request->addOnSubscriptionPlanUuid . '&invoice_id=' . $invoice->uuid);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/failed?go_back_url=' . $request['goBackUrl'] . '&addOnSubscriptionPlanUuid=' . $request->addOnSubscriptionPlanUuid);
        }
    }

    public function cancelPaymentSubscriptionAddOn(Request $request)
    {
        return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/canceled?go_back_url=' . $request['goBackUrl'] . '&addOnUuid=' . $request['addOnUuid']);
    }

    public function cancelPayment(Request $request)
    {

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/cancel?packageID=' . $request->creditPackageUuid);
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
            $invoice = $this->invoiceService->create($invoiceData);
            Event::dispatch(new PaymentCreditPackageSuccessEvent($request->creditPackageUuid, $paymentData, $request->userUuid, PaymentMethod::STRIPE, $invoice));
            Event::dispatch(new SendNotificationSystemForPaymentEvent([
                'credit_package_uuid' => $request->creditPackageUuid,
                'user_uuid' => $request->userUuid,
                'payment_method_uuid' => PaymentMethod::STRIPE
            ], Notification::CREDIT_TYPE));
            return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/success?go_back_url=' . $request->goBackUrl . '&package_id=' . $request->creditPackageUuid . '&invoice_id=' . $invoice->uuid);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/failed?go_back_url=' . $request->goBackUrl . '&package_id=' . $request->creditPackageUuid);
        }
    }
}
