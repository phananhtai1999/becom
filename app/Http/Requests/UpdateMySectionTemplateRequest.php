<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMySectionTemplateRequest extends AbstractRequest
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
            'section_category_uuid' => ['numeric', Rule::exists('section_categories','uuid')->whereNull('deleted_at')],
            'type' => ['string', 'in:modal,in_page']
        ];
    }
}
