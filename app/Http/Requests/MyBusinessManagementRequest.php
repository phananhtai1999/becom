<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class MyBusinessManagementRequest extends AbstractRequest
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
            'name' => ['required', 'string'],
            'introduce' => ['required', 'string'],
            'products_services' => ['required', 'array'],
            'products_services.products' => ['required', 'array'],
            'products_services.products.*' => ['nullable', 'string'],
            'products_services.services' => ['required', 'array'],
            'products_services.services.*' => ['nullable', 'string'],
            'customers' => ['required', 'array'],
            'customers.*' => ['string'],
            'business_categories' => ['nullable', 'array', 'min:1'],
            'business_categories.*' => ['numeric', 'min:1', Rule::exists('business_categories', 'uuid')->whereNull('deleted_at')],
            'domain_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('domains', 'uuid')->where(function ($query) {
                return $query->where([
                    ['owner_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')],
            'domain' => ['required', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/'],
            'avatar' => ['required', 'string'],
            'slogan' => ['required', 'string'],
            's3_option' => ['nullable', 'boolean']
        ];

        if (is_array($this->request->get('products_services'))) {
            foreach ($this->request->get('products_services') as $key => $value) {
                if (!in_array($key, ['services', 'products'])) {
                    $validate["products_services.$key"] = ['in:products,services'];
                }
            }
        }

        if ($this->request->get('s3_option')){
            $validate['access_key'] = ['required', 'string'];
            $validate['secret_access_key'] = ['required', 'string'];
            $validate['default_region'] = ['required', 'string'];
            $validate['bucket'] = ['required', 'string'];
        }

        return $validate;
    }
}
