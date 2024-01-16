<?php

namespace App\Listeners;

use App\Events\SendNotificationSystemEvent;
use App\Events\SendNotificationSystemForPaymentEvent;
use App\Mail\SendNotificationSystem;
use App\Models\Notification;
use App\Services\AddOnService;
use App\Services\AddOnSubscriptionPlanService;
use App\Services\CreditPackageService;
use App\Services\NotificationService;
use App\Services\PaymentMethodService;
use App\Services\SmtpAccountService;
use App\Services\SubscriptionPlanService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationSystemForPaymentListener implements ShouldQueue
{
    private $smtpAccountService;

    private $notificationService;

    private $addOnSubscriptionPlanService;
    private $subscriptionPlanService;
    private $paymentMethodService;
    private $creditPackageService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        SmtpAccountService $smtpAccountService,
        NotificationService $notificationService,
        AddOnSubscriptionPlanService $addOnSubscriptionPlanService,
        PaymentMethodService $paymentMethodService,
        SubscriptionPlanService $subscriptionPlanService,
        CreditPackageService $creditPackageService
    )
    {
        $this->smtpAccountService = $smtpAccountService;
        $this->notificationService = $notificationService;
        $this->addOnSubscriptionPlanService = $addOnSubscriptionPlanService;
        $this->paymentMethodService = $paymentMethodService;
        $this->subscriptionPlanService = $subscriptionPlanService;
        $this->creditPackageService = $creditPackageService;
    }

    /**
     * @param SendNotificationSystemForPaymentEvent $event
     * @return void
     */
    public function handle(SendNotificationSystemForPaymentEvent $event)
    {
        $dataPayment = $event->dataPayment;
        $featurePayment = $event->featurePayment;

        $paymentMethod = $this->paymentMethodService->findOrFailById($dataPayment['payment_method_uuid']);
        if ($featurePayment === Notification::ADDON_TYPE){
            $addOnSubscriptionPlan = $this->addOnSubscriptionPlanService->findOrFailById($dataPayment['add_on_subscription_plan_uuid']);
            $addOn = optional($addOnSubscriptionPlan)->addOn;
            $price = $addOnSubscriptionPlan->duration_type === 'month' ? $addOn->monthly : $addOn->yearly;

            $type = "addon";
            $action = "payment";
            $content = [
                'langkey' => $type.'_'.$action,
                'type' => $type,
                'name' => $addOn->name,
                'price' => $price,
                'type_payment' => $paymentMethod->name];

        }elseif ($featurePayment === Notification::PACKAGE_TYPE){
            $subscriptionPlan = $this->subscriptionPlanService->findOrFailById($dataPayment['subscription_plan_uuid']);
            $platformPackage = optional($subscriptionPlan)->platformPackage;
            $price = $subscriptionPlan->duration_type === 'month' ? $platformPackage->monthly : $platformPackage->yearly;

            $type = "package";
            $action = "payment";
            $content = [
                'langkey' => $type.'_'.$action,
                'type' => $type,
                'name' => $platformPackage->uuid,
                'price' => $price,
                'type_payment' => $paymentMethod->name];
        }elseif ($featurePayment === Notification::CREDIT_TYPE){
            $creditPackage = $this->creditPackageService->findOrFailById($dataPayment['credit_package_uuid']);
            $price = $creditPackage->price;
            $credit = $creditPackage->credit;

            $type = "credit";
            $action = "payment";
            $content = [
                'langkey' => $type.'_'.$action,
                'type' => $type,
                'price' => $price,
                'credit' => $credit,
                'type_payment' => $paymentMethod->name];
        }

        $this->notificationService->create([
            'type' => 'payment',
            'type_uuid' => null,
            'content' => $content,
            'user_uuid' => $dataPayment['user_uuid'],
            'app_id' => $dataPayment['app_id'],
        ]);
    }
}
