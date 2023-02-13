<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

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
            'domain' => ['string', Rule::unique('websites')->ignore($this->id, 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getKey())->whereNull('deleted_at');
            })],
            'name' => ['string'],
            'description' => ['string'],
            'logo' => ['string'],
        ];
    }
}
