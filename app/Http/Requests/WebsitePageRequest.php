<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class WebsitePageRequest extends AbstractRequest
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
            'title' => ['required', 'string'],
            'template' => ['required', 'string'],
            'template_json' => ['required', 'string'],
            'website_page_category_uuid' => ['required', 'numeric', Rule::exists('website_page_categories','uuid')->whereNull('deleted_at')],
            'publish_status' => ['required', 'numeric', 'min:1', 'max:2'],
            'user_uuid' => ['nullable', 'numeric', Rule::exists('users','uuid')->whereNull('deleted_at')],
        ];
    }
}
