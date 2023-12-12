<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateUserTrackingRequest extends AbstractRequest
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
            'ip' => ['string'],
            'location' => ['string'],
            'user_uuid' => ['numeric', 'exists:user_profiles,uuid'],
            'postal_code' => ['string'],
        ];
    }
}
