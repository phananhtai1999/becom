<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateFormRequest extends AbstractRequest
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
            'title' => ['string'],
            'template' => ['string'],
            'template_json' => ['string'],
            'contact_list_uuid' => ['nullable', 'numeric', Rule::exists('contact_lists','uuid')->whereNull('deleted_at')],
            'publish_status' => ['numeric', 'min:1', 'max:4'],
            'display_type' => ['string', 'in:modal,in_page']
        ];
    }
}
