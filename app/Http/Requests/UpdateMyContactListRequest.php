<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMyContactListRequest extends AbstractRequest
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
            'contact.*' => ['numeric', 'min:1', Rule::exists('contacts', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
        ];
    }
}
