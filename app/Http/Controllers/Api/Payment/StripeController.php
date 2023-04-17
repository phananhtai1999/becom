<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\UpdateCardCustomerRequest;
use App\Http\Requests\UpdateCardStripeRequest;
use App\Models\PaymentMethod;
use App\Services\StripeService;
use App\Services\SubscriptionHistoryService;

class StripeController extends AbstractRestAPIController
{

    const UPDATE_CARD_STRIPE = 'update';
    const ADD_CARD_STRIPE = 'add';

    public function __construct(
        StripeService              $service,
        SubscriptionHistoryService $subscriptionHistoryService,
    )
    {
        $this->service = $service;
        $this->subscriptionHistoryService = $subscriptionHistoryService;
    }

    public function cardStripe(UpdateCardCustomerRequest $request)
    {
        $customer = $this->getCustomer();
        $token = $this->service->createNewToken($request);
        if ($request->get('type') == self::UPDATE_CARD_STRIPE) {
            $this->service->updateCustomerCard($customer->id, $token);
            $message = 'Update Card Successfully';
        } else {
            $this->service->addCard($customer->id, $token);
            $message = 'Add new Card Successfully';
        }

        return $this->sendOkJsonResponse(['message' => $message]);
    }

    public function allCardStripe()
    {
        $customer = $this->getCustomer();
        $allCard = $this->service->allCard($customer->id);

        return $this->sendOkJsonResponse(['data' => $allCard]);
    }

    public function updateCard(UpdateCardStripeRequest $request, $id)
    {
        $customer = $this->getCustomer();
        $card = $this->service->updateOneCard($customer->id, $request, $id);

        return $this->sendOkJsonResponse(['data' => $card]);
    }

    public function getCustomer() {
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
}
