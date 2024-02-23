<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\DomainService;

class BusinessManagementResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->getKey(),
            'name' => $this->name,
            'introduce' => $this->introduce,
            'products_services' => $this->products_services,
            'customers' => $this->customers,
            'avatar' => $this->avatar,
            'slogan' => $this->slogan,
            'owner_uuid' => $this->owner_uuid,
            'domain_uuid' => $this->domain_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('business_management__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('business_management__business_categories', $expand)) {
            $data['business_categories'] = BusinessCategoryResource::collection($this->businessCategories);
        }

        if (\in_array('business_management__domains', $expand)) {
            $data['domains'] = DomainResource::collection($this->domains);
        }

        if (\in_array('business_management__domain', $expand)) {
            $data['domain'] = DomainResource::collection($this->domain);
        }

        if (\in_array('business_management__domain_default', $expand)) {
            $data['domain_default'] = app(DomainService::class)->findDomainByUuid($this->domain_uuid);
        }

        return $data;
    }
}
