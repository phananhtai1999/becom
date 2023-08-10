<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessManagementRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validate = [
            'name' => ['string'],
            'introduce' => ['string'],
            'products_services' => ['array'],
            'products_services.products' => ['array'],
            'products_services.products.*' => ['nullable', 'string'],
            'products_services.services' => ['array'],
            'products_services.services.*' => ['nullable', 'string'],
            'customers' => ['array'],
            'customers.*' => ['string'],
            'owner_uuid' => ['numeric', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'business_categories' => ['nullable', 'array', 'min:1'],
            'business_categories.*' => ['numeric', 'min:1', Rule::exists('business_categories', 'uuid')->whereNull('deleted_at')],
            'domain_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('domains', 'uuid')->where(function ($query) {
                return $query->where('owner_uuid', $this->request->get('owner_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
            'domain' => ['nullable', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/'],
            'avatar' => ['string'],
            'slogan' => ['string'],
        ];

        if (is_array($this->request->get('products_services'))) {
            foreach ($this->request->get('products_services') as $key => $value) {
                if (!in_array($key, ['services', 'products'])) {
                    $validate["products_services.$key"] = ['in:products,services'];
                }
            }
        }

        return $validate;
    }
}
