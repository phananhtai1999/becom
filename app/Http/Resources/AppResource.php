<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Techup\ApiList\Http\Resources\GroupApiListResource;
use Techup\ApiList\Models\GroupApiList;

class AppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'monthly' => $this->monthly,
            'name' => $this->name,
            'service' => $this->service,
            'group_api_codes' => $this->group_api_codes,
            'parent_uuid' => $this->parent_uuid,
            'yearly' => $this->yearly,
            'description' => $this->description,
            'avatar' => $this->avatar,
            'payment_product_id' => $this->payment_product_id,
            'status' => $this->status,
        ];
        if (\in_array('app__permissions', $expand)) {
            $data['permissions'] = PermissionResource::collection($this->permissions);
        }

        if (\in_array('app__groups', $expand)) {
            $groupApis = $this->group_api_codes ?? [];
            foreach ($groupApis as $groupApi) {
                $data['group_apis'][] = new GroupApiListResource(GroupApiList::where('code', $groupApi)->first() ?? []);
            }
        }

        if (\in_array('app__subscription_plan', $expand)) {
            $data['subscription_plan'] = SubscriptionPlanResource::collection($this->subscriptionPlans);
        }
        if (\in_array('app__add_on', $expand)) {
            $data['add_on'] = AddOnResource::collection($this->addOns);
        }
        if (\in_array('app__parent', $expand)) {
            $data['parent'] = new AppResource($this->parentApp);
        }

        if (\in_array('app__children', $expand)) {
            $data['children'] = self::collection($this->childrenApp);
        }
        return $data;
    }
}
