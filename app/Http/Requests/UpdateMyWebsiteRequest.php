<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateMyWebsiteRequest extends AbstractRequest
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
            'domain' => ['string', 'unique:websites,domain,'.$this->id .',uuid,deleted_at,NULL'],
            'name' => ['string'],
            'description' => ['string'],
            'logo' => ['string'],
        ];
    }
}
