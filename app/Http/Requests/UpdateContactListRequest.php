<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateContactListRequest extends AbstractRequest
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
            'file' => ['nullable', 'mimes:xlsx,csv,json,js'],
            'name' => ['string'],
            'contact' => ['nullable', 'array', 'min:1'],
            'contact.*' => ['numeric', 'min:1', Rule::exists('contacts', 'uuid')->whereNull('deleted_at')],
            'user_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('user_profiles', 'uuid')->whereNull('deleted_at')],
        ];
    }
}
