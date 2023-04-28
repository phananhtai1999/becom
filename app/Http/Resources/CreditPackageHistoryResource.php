<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\PaymentMethodController;
use App\Models\CreditPackage;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditPackageHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'user_uuid' => $this->user_uuid,
            'payment_method_uuid' => $this->payment_method_uuid,
            'credit_package_uuid' => $this->credit_package_uuid,
            'invoice_uuid' => $this->invoice_uuid,
            'logs' => $this->logs,
        ];
        if (\in_array('credit_package_history__credit_package', $expand)) {
            $data['credit_package'] = new CreditPackageResource($this->creditPackage);
        }
        if (\in_array('credit_package_history__payment_method', $expand)) {
            $data['payment_method'] = new PaymentMethodResource($this->paymentMethod);
        }
        if (\in_array('credit_package_history__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }
        if (\in_array('credit_package__invoice', $expand)) {
            $data['invoice'] = new InvoiceResource($this->invoice);
        }

        return $data;
    }
}
