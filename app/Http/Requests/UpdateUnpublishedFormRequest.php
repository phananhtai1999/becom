<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Form;
use Illuminate\Validation\Rule;

class UpdateUnpublishedFormRequest extends AbstractRequest
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
            'contact_list_uuid' => ['nullable', 'numeric', Rule::exists('contact_lists','uuid')->whereNull('deleted_at')],
            'display_type' => ['string', 'in:modal,in_page'],
            'publish_status' => ['numeric', Rule::in(Form::PENDING_PUBLISH_STATUS, Form::DRAFT_PUBLISH_STATUS)],
        ];
    }
}
