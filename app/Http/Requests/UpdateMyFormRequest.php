<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMyFormRequest extends AbstractRequest
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
            'html_template' => ['string'],
            'css_template' => ['string'],
            'js_template' => ['string'],
            'template_json' => ['string'],
            'contact_list_uuid' => ['numeric', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])->whereNull('deleted_at');
            })],
            'display_type' => ['string', 'in:modal,in_page']
        ];
    }
}
