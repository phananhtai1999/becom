<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMyDomainRequest extends AbstractRequest
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
        return [
            'name' => ['string', 'regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/'],
            'verified_at' => ['nullable', 'date'],
            'business_uuid' => ['nullable', 'numeric', Rule::exists('business_managements', 'uuid')->where(function ($query) {
                return $query->where('owner_uuid', auth()->user()->getKey());
            })
                ->whereNull('deleted_at')],
        ];
    }
}
